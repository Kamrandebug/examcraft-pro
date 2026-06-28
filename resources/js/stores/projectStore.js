import { defineStore } from 'pinia';
import { ref } from 'vue';

const DB_NAME = 'ExamCraftProDB';
const DB_VERSION = 1;
const STORE_NAME = 'projects';

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id' });
            }
        };
    });
}

export const useProjectStore = defineStore('project', () => {
    const projects = ref([]);
    const currentProjectId = ref(null);
    const autoSaveTimer = ref(null);

    // IndexedDB Helper Operations
    const getAllProjectsFromDB = async () => {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(STORE_NAME, 'readonly');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.getAll();
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    };

    const saveProjectToDB = async (project) => {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(STORE_NAME, 'readwrite');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.put(project);
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    };

    const deleteProjectFromDB = async (id) => {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(STORE_NAME, 'readwrite');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.delete(id);
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    };

    const getProjectFromDB = async (id) => {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(STORE_NAME, 'readonly');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.get(id);
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    };

    // Pinia Actions
    const fetchAllProjects = async () => {
        try {
            projects.value = await getAllProjectsFromDB();
        } catch (err) {
            console.error('Failed to fetch projects', err);
        }
    };

    const saveProject = async (projectData) => {
        if (!projectData.id) {
            projectData.id = 'proj_' + Math.random().toString(36).substr(2, 9);
        }
        projectData.updatedAt = new Date().toISOString();
        
        try {
            await saveProjectToDB(projectData);
            await fetchAllProjects();
            currentProjectId.value = projectData.id;
        } catch (err) {
            console.error('Failed to save project', err);
        }
    };

    const loadProject = async (projectId) => {
        try {
            const project = await getProjectFromDB(projectId);
            if (project) {
                currentProjectId.value = projectId;
                return project;
            }
        } catch (err) {
            console.error('Failed to load project', err);
        }
        return null;
    };

    const deleteProject = async (projectId) => {
        try {
            await deleteProjectFromDB(projectId);
            await fetchAllProjects();
            if (currentProjectId.value === projectId) {
                currentProjectId.value = null;
            }
        } catch (err) {
            console.error('Failed to delete project', err);
        }
    };

    const exportJSON = (projectData) => {
        const jsonString = JSON.stringify(projectData, null, 2);
        const blob = new Blob([jsonString], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${projectData.name || 'examcraft-project'}.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    };

    const importJSON = async (file) => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = async (e) => {
                try {
                    const projectData = JSON.parse(e.target.result);
                    if (!projectData.name || !projectData.pages) {
                        throw new Error('Invalid project structure');
                    }
                    await saveProject(projectData);
                    resolve(projectData);
                } catch (err) {
                    console.error('Failed to import project', err);
                    reject(err);
                }
            };
            reader.onerror = () => reject(reader.error);
            reader.readAsText(file);
        });
    };

    return {
        projects,
        currentProjectId,
        autoSaveTimer,
        fetchAllProjects,
        saveProject,
        loadProject,
        deleteProject,
        exportJSON,
        importJSON
    };
});
