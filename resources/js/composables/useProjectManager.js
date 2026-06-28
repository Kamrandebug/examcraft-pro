import { useExamStore } from '../stores/examStore';
import { useTypoStore } from '../stores/typoStore';
import { useProjectStore } from '../stores/projectStore';
import { useTypography } from './useTypography';
import { useToast } from './useToast';

export function useProjectManager() {
    const examStore = useExamStore();
    const typoStore = useTypoStore();
    const projectStore = useProjectStore();
    const { applyTypoToPaper } = useTypography();
    const { showToast } = useToast();

    async function saveProject(name) {
        const projectData = {
            id: projectStore.currentProjectId || 'proj-' + Date.now(),
            name: name || examStore.paperMeta?.title || 'Untitled',
            savedAt: new Date().toISOString(),
            data: {
                pages: JSON.parse(JSON.stringify(examStore.pages)),
                paperMeta: { ...examStore.paperMeta },
                typoState: { ...typoStore.typoState },
                globalOpts: { ...examStore.globalOpts },
                styleState: { ...examStore.styleState },
                coverFooter: { ...examStore.coverFooter },
                pageFooter: { ...examStore.pageFooter }
            }
        };
        
        await projectStore.saveProject(projectData);
        showToast('Project saved!', 'success');
        return projectData;
    }

    async function loadAllProjects() {
        await projectStore.fetchAllProjects();
        return projectStore.projects;
    }

    async function loadProject(id) {
        const project = await projectStore.loadProject(id);
        if (project && project.data) {
            examStore.pages = project.data.pages || [];
            examStore.paperMeta = { ...examStore.paperMeta, ...project.data.paperMeta };
            examStore.globalOpts = { ...examStore.globalOpts, ...project.data.globalOpts };
            examStore.styleState = { ...examStore.styleState, ...project.data.styleState };
            examStore.coverFooter = { ...examStore.coverFooter, ...project.data.coverFooter };
            examStore.pageFooter = { ...examStore.pageFooter, ...project.data.pageFooter };
            
            Object.assign(typoStore.typoState, project.data.typoState);
            
            projectStore.currentProjectId = id;
            applyTypoToPaper();
            showToast('Project loaded!', 'success');
        }
        return project;
    }

    async function deleteProject(id) {
        await projectStore.deleteProject(id);
        showToast('Project deleted', 'success');
    }

    function exportJSON() {
        const data = {
            version: 'v36',
            exportedAt: new Date().toISOString(),
            name: examStore.paperMeta?.title || 'examcraft',
            pages: examStore.pages,
            paperMeta: examStore.paperMeta,
            typoState: typoStore.typoState,
            styleState: examStore.styleState,
            globalOpts: examStore.globalOpts
        };
        projectStore.exportJSON(data);
        showToast('Exported successfully', 'success');
    }

    async function importJSON(file) {
        try {
            const data = await projectStore.importJSON(file);
            if (data && data.pages) {
                examStore.pages = data.pages;
                examStore.paperMeta = data.paperMeta;
                Object.assign(typoStore.typoState, data.typoState);
                applyTypoToPaper();
                showToast('Project imported successfully', 'success');
            }
        } catch (err) {
            showToast('Invalid project file', 'error');
            console.error(err);
        }
    }

    return {
        saveProject, loadAllProjects, loadProject,
        deleteProject, exportJSON, importJSON
    };
}
