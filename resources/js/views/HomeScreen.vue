<template>
  <div class="home-screen">
    <div class="home-inner">
      <div class="home-header">
        <h2 class="home-title">ExamCraft Pro</h2>
        <p class="home-subtitle">Professional Exam Authoring</p>
        <a v-if="window.authUser?.role !== 'admin'" href="/user/dashboard" class="home-dashboard-link">
          <i class="fa fa-th-large"></i> Back to My Dashboard
        </a>
      </div>

      <div class="row g-4 home-cards-row">
        <div class="col-md-6">
          <div class="card home-card" @click="uiStore.setView('manual')">
            <div class="card-body">
              <div class="home-card-icon">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="6" y="4" width="36" height="40" rx="2" stroke="currentColor" stroke-width="2"/>
                  <line x1="14" y1="14" x2="34" y2="14" stroke="currentColor" stroke-width="1.5"/>
                  <line x1="14" y1="20" x2="30" y2="20" stroke="currentColor" stroke-width="1.5"/>
                  <line x1="14" y1="26" x2="32" y2="26" stroke="currentColor" stroke-width="1.5"/>
                  <line x1="14" y1="32" x2="28" y2="32" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </div>
              <h5 class="home-card-title">Create Manual Paper</h5>
              <p class="home-card-desc">Full design control over every block. Add MCQs, sections, images, tables, and more with drag-and-drop precision.</p>
              <button class="btn home-card-btn home-card-btn-primary">Start Designing</button>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card home-card" @click="uiStore.setView('auto')">
            <div class="card-body">
              <div class="home-card-icon">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 36V12C8 10.9 8.9 10 10 10H38C39.1 10 40 10.9 40 12V36C40 37.1 39.1 38 38 38H10C8.9 38 8 37.1 8 36Z" stroke="currentColor" stroke-width="2"/>
                  <path d="M14 18L18 22L14 26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <line x1="22" y1="24" x2="34" y2="24" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="36" cy="34" r="6" fill="none" stroke="currentColor" stroke-width="1.5"/>
                  <path d="M34.5 32L36 34L38.5 31" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <h5 class="home-card-title">Auto Paper Generator</h5>
              <p class="home-card-desc">Select MCQs from the question bank by grade and subject, and let the system build a formatted paper automatically.</p>
              <button class="btn home-card-btn home-card-btn-secondary">Generate Paper</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useUiStore } from '../stores/uiStore';

const uiStore = useUiStore();
</script>

<style scoped>
.home-screen {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-primary);
  overflow: auto;
}

.home-inner {
  max-width: 800px;
  width: 100%;
  padding: 40px 20px;
}

.home-header {
  text-align: center;
  margin-bottom: 40px;
}

.home-title {
  font-family: var(--font-display);
  font-size: 36px;
  font-weight: 700;
  color: var(--text-primary);
  letter-spacing: 0.03em;
  margin-bottom: 4px;
}

.home-subtitle {
  font-family: var(--font-body);
  font-size: 15px;
  color: var(--text-muted);
  letter-spacing: 0.04em;
  margin: 0;
}

.home-dashboard-link {
  display: inline-block;
  margin-top: 14px;
  font-family: var(--font-body);
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
  text-decoration: none;
  padding: 6px 14px;
  border: 1px solid var(--border-light);
  border-radius: var(--radius-sm);
  transition: var(--transition);
}

.home-dashboard-link:hover {
  border-color: var(--accent-2);
  color: var(--accent-2);
}

.home-cards-row {
  margin: 0 auto;
  justify-content: center;
}

.home-card {
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-radius: var(--radius);
  cursor: pointer;
  transition: var(--transition);
  height: 100%;
}

.home-card:hover {
  border-color: var(--accent-2);
  background: var(--bg-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.home-card-icon {
  color: var(--accent-2);
  margin-bottom: 16px;
}

.home-card-title {
  font-family: var(--font-body);
  font-size: 18px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.home-card-desc {
  font-family: var(--font-body);
  font-size: 13px;
  color: var(--text-muted);
  line-height: 1.6;
  margin-bottom: 16px;
}

.home-card-btn {
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 600;
  padding: 8px 18px;
  border-radius: var(--radius-sm);
  transition: var(--transition);
  border: none;
}

.home-card-btn-primary {
  background: var(--accent-2);
  color: #fff;
}

.home-card-btn-primary:hover {
  filter: brightness(1.15);
}

.home-card-btn-secondary {
  background: var(--accent);
  color: #fff;
}

.home-card-btn-secondary:hover {
  filter: brightness(1.15);
}
</style>
