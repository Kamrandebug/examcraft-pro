<template>
  <div id="right-panel" :style="{ width: uiStore.rightCollapsed ? '0px' : uiStore.rightPanelWidth + 'px' }">
    <div id="rpanel-header" 
      style="display:flex;flex-direction:column; 
      border-bottom:1px solid var(--border);flex-shrink:0;"> 

      <!-- Icon tab bar — EXACTLY as original --> 
      <div style="display:flex;align-items:center;gap:0; 
        padding:4px 6px 0;background:var(--bg-secondary);"> 

        <div class="rpanel-icontab" 
          :class="{ active: uiStore.activeRTab === 'properties' }" 
          @click="switchRTab('properties')" title="Properties"> 
          <i class="fa fa-sliders-h"></i> 
          <span>Properties</span> 
        </div> 

        <div class="rpanel-icontab" 
          :class="{ active: uiStore.activeRTab === 'answerkey' }" 
          @click="switchRTab('answerkey')" title="Answer Key"> 
          <i class="fa fa-check-circle"></i> 
          <span>Answers</span> 
        </div> 

        <div class="rpanel-icontab" 
          :class="{ active: uiStore.activeRTab === 'typography' }" 
          @click="switchRTab('typography')" title="Typography & Fonts"> 
          <i class="fa fa-font"></i> 
          <span>Fonts</span> 
        </div> 

        <div class="rpanel-icontab" 
          :class="{ active: uiStore.activeRTab === 'style' }" 
          @click="switchRTab('style')" title="Page Style & Layout"> 
          <i class="fa fa-paint-brush"></i> 
          <span>Style</span> 
        </div> 

        <div class="rpanel-icontab" 
          :class="{ active: uiStore.activeRTab === 'coverfooter' }" 
          @click="switchRTab('coverfooter')" title="Cover Page Footer"> 
          <i class="fa fa-file-alt"></i> 
          <span>Cover Footer</span> 
        </div> 

        <div class="rpanel-icontab" 
          :class="{ active: uiStore.activeRTab === 'pagefooter' }" 
          @click="switchRTab('pagefooter')" title="Content Pages Footer"> 
          <i class="fa fa-copy"></i> 
          <span>Page Footer</span> 
        </div> 

        <div style="flex:1;"></div> 

        <button class="panel-collapse-btn" 
          @click="togglePanel('right')" 
          title="Collapse panel"> 
          <i class="fa fa-chevron-right" id="right-collapse-icon"></i> 
        </button> 
      </div> 

      <!-- Active tab label strip --> 
      <div id="rpanel-tab-label" 
        style="padding:3px 10px 4px;font-size:10px;font-weight:700; 
        color:var(--accent);letter-spacing:0.06em;text-transform:uppercase; 
        background:var(--bg-secondary); 
        border-top:1px solid var(--border-light);"> 
        {{ tabLabels[uiStore.activeRTab] }} 
      </div> 
    </div> 

    <!-- TAB 1: Properties --> 
    <div class="rpanel-content" id="rtab-properties" 
      v-show="uiStore.activeRTab === 'properties'"> 
      <div class="no-selection" v-show="!examStore.selectedBlockId"> 
        <i class="fa fa-mouse-pointer"></i> 
        <p>Click any block on the canvas to edit its properties here.</p> 
      </div> 
      <div id="properties-form" v-show="examStore.selectedBlockId"> 
        <!-- Dynamic per-block properties rendered here --> 
        <PropertiesPanel v-if="examStore.selectedBlockId" /> 
      </div> 
    </div> 

    <!-- TAB 2: Answer Key --> 
    <div class="rpanel-content" id="rtab-answerkey" 
      v-show="uiStore.activeRTab === 'answerkey'"> 
      <div style="margin-bottom:10px;display:flex;align-items:center; 
        justify-content:space-between;"> 
        <span style="font-size:12px;color:var(--text-muted); 
          font-weight:600;">Answer Key</span> 
        <button class="btn-sm-dark" @click="exportAnswerKey()"> 
          <i class="fa fa-download"></i> Export 
        </button> 
      </div> 
      <div id="answer-key-list"> 
        <div v-for="block in allMcqBlocks" :key="block.id" class="ak-item"> 
          <span class="ak-num">{{ block.qNum }}</span> 
          <div class="ak-ans"> 
            <div v-for="letter in ['A','B','C','D']" :key="letter" 
              class="ak-opt" 
              :class="{ selected: block.correct === letter }" 
              @click="block.correct = letter"> 
              {{ letter }} 
            </div> 
          </div> 
        </div> 
      </div> 
    </div> 

    <!-- TAB 3: Typography --> 
    <div class="rpanel-content" id="rtab-typography" 
      v-show="uiStore.activeRTab === 'typography'"> 

      <div class="typo-section-label"> 
        <i class="fa fa-star"></i> Cambridge Presets 
      </div> 
      <div class="typo-preset-grid" id="typo-preset-grid"> 
        <div v-for="preset in typoStore.TYPO_PRESETS" :key="preset.id" 
          class="typo-preset-card" 
          :id="'typo-preset-' + preset.id" 
          :class="{ active: typoStore.typoState.bodyFont === preset.font }" 
          @click="applyTypoPreset(preset.id)" 
          :title="preset.name"> 
          <div class="typo-preset-name">{{ preset.name }}</div> 
          <div class="typo-preset-desc" v-html="preset.desc.replace(/\n/g,'<br>')"> 
          </div> 
          <div class="typo-preset-sample" :style="{ fontFamily: preset.font }"> 
            {{ preset.sample }} 
          </div> 
        </div> 
      </div> 

      <div class="typo-section-label"> 
        <i class="fa fa-eye"></i> Live Preview 
      </div> 
      <div class="typo-preview-box" id="typo-preview-box" :style="{
          fontFamily: typoStore.typoState.bodyFont,
          fontSize: typoStore.typoState.qFontSize + 'pt',
          lineHeight: typoStore.typoState.lineHeight,
          wordSpacing: typoStore.typoState.wordSpacing + 'px',
          letterSpacing: typoStore.typoState.letterSpacing + 'px'
      }"> 
        <div class="typo-preview-q" id="typo-preview-q" :style="{
            fontSize: typoStore.typoState.qFontSize + 'pt',
            fontWeight: typoStore.typoState.qWeight
        }"> 
          <b>1</b>&nbsp; Which of the following is a vector quantity? 
        </div> 
        <div class="typo-preview-opt" id="typo-preview-opt" :style="{
            fontSize: typoStore.typoState.optFontSize + 'pt',
            marginTop: typoStore.typoState.paraSpacing + 'px'
        }"> 
          <b>A</b>&nbsp; distance &nbsp;&nbsp; <b>B</b>&nbsp; speed 
          <br><b>C</b>&nbsp; mass &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
          <b>D</b>&nbsp; velocity 
        </div> 
      </div> 

      <div class="typo-section-label" style="margin-top:14px;"> 
        <i class="fa fa-text-height"></i> Font Families 
      </div> 

      <div class="prop-group" style="margin-bottom:10px;"> 
        <label class="prop-label">Body & Question Font</label> 
        <div class="font-select-wrap"> 
          <select id="typo-body-font" 
            v-model="typoStore.typoState.bodyFont" 
            @change="applyTypoFont('body', $event.target.value)"> 
            <optgroup label="── Cambridge Standard ──"> 
              <option value="Arial, sans-serif">Arial (Cambridge Default)</option> 
              <option value="'Times New Roman', serif">Times New Roman (O Level Classic)</option> 
              <option value="'EB Garamond', Garamond, serif">EB Garamond (A Level Style)</option> 
              <option value="'Crimson Text', Georgia, serif">Crimson Text (Elegant Serif)</option> 
              <option value="'Libre Baskerville', Baskerville, serif">Libre Baskerville (Book Style)</option> 
              <option value="'Source Serif 4', Georgia, serif">Source Serif 4 (Modern Academic)</option> 
            </optgroup> 
            <optgroup label="── Serif Fonts ──"> 
              <option value="Georgia, serif">Georgia</option> 
              <option value="'Merriweather', serif">Merriweather</option> 
              <option value="'Lora', serif">Lora</option> 
              <option value="'PT Serif', serif">PT Serif</option> 
              <option value="'Noto Serif', serif">Noto Serif</option> 
            </optgroup> 
            <optgroup label="── Sans-Serif Fonts ──"> 
              <option value="'Helvetica Neue', Helvetica, Arial, sans-serif">Helvetica Neue</option> 
              <option value="Verdana, sans-serif">Verdana</option> 
              <option value="Tahoma, sans-serif">Tahoma</option> 
              <option value="Calibri, sans-serif">Calibri</option> 
              <option value="'DM Sans', sans-serif">DM Sans</option> 
            </optgroup> 
          </select> 
        </div> 
        <div class="font-preview-strip" id="font-preview-body" 
          :style="{ fontFamily: typoStore.typoState.bodyFont }"> 
          The quick brown fox jumps over a lazy dog. 0123456789 
        </div> 
      </div> 

      <div class="prop-group" style="margin-bottom:10px;"> 
        <label class="prop-label">Header & Title Font</label> 
        <div class="font-select-wrap"> 
          <select id="typo-header-font" 
            v-model="typoStore.typoState.headerFont" 
            @change="applyTypoFont('header', $event.target.value)"> 
            <optgroup label="── Cambridge Standard ──"> 
              <option value="Arial, sans-serif">Arial (Cambridge Default)</option> 
              <option value="'Times New Roman', serif">Times New Roman</option> 
              <option value="'EB Garamond', Garamond, serif">EB Garamond</option> 
              <option value="'Libre Baskerville', Baskerville, serif">Libre Baskerville</option> 
            </optgroup> 
            <optgroup label="── Sans-Serif ──"> 
              <option value="'Helvetica Neue', Helvetica, Arial, sans-serif">Helvetica Neue</option> 
              <option value="Verdana, sans-serif">Verdana</option> 
              <option value="Tahoma, sans-serif">Tahoma</option> 
              <option value="Calibri, sans-serif">Calibri</option> 
            </optgroup> 
            <optgroup label="── Serif ──"> 
              <option value="Georgia, serif">Georgia</option> 
              <option value="'Merriweather', serif">Merriweather</option> 
              <option value="'Lora', serif">Lora</option> 
            </optgroup> 
          </select> 
        </div> 
        <div class="font-preview-strip" id="font-preview-header" 
          :style="{ fontFamily: typoStore.typoState.headerFont, fontWeight: 700 }"> 
          PHYSICS — Cambridge O Level 
        </div> 
      </div> 

      <div class="typo-section-label"> 
        <i class="fa fa-sort-amount-up"></i> Font Sizes 
      </div> 

      <div class="typo-slider-row"> 
        <label>Question Text</label> 
        <input type="range" id="typo-q-size" min="8" max="16" step="0.5" 
          :value="typoStore.typoState.qFontSize" 
          @input="applyTypoSize('q', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-q-size"> 
          {{ typoStore.typoState.qFontSize }}pt 
        </span> 
      </div> 
      <div class="typo-slider-row"> 
        <label>Options Text</label> 
        <input type="range" id="typo-opt-size" min="8" max="16" step="0.5" 
          :value="typoStore.typoState.optFontSize" 
          @input="applyTypoSize('opt', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-opt-size"> 
          {{ typoStore.typoState.optFontSize }}pt 
        </span> 
      </div> 
      <div class="typo-slider-row"> 
        <label>Section Header</label> 
        <input type="range" id="typo-section-size" min="9" max="18" step="0.5" 
          :value="typoStore.typoState.sectionSize" 
          @input="applyTypoSize('section', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-section-size"> 
          {{ typoStore.typoState.sectionSize }}pt 
        </span> 
      </div> 
      <div class="typo-slider-row"> 
        <label>Paper Header</label> 
        <input type="range" id="typo-header-size" min="9" max="20" step="0.5" 
          :value="typoStore.typoState.headerSize" 
          @input="applyTypoSize('header', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-header-size"> 
          {{ typoStore.typoState.headerSize }}pt 
        </span> 
      </div> 
      <div class="typo-slider-row"> 
        <label>Footer Text</label> 
        <input type="range" id="typo-footer-size" min="7" max="13" step="0.5" 
          :value="typoStore.typoState.footerSize || 10" 
          @input="applyTypoSize('footer', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-footer-size"> 
          {{ typoStore.typoState.footerSize || 10 }}pt 
        </span> 
      </div> 

      <div class="typo-section-label"> 
        <i class="fa fa-arrows-alt-v"></i> Spacing 
      </div> 

      <div class="typo-slider-row"> 
        <label>Line Height</label> 
        <input type="range" id="typo-line-height" 
          min="1.0" max="2.5" step="0.05" 
          :value="typoStore.typoState.lineHeight" 
          @input="applyTypoSpacing('lineHeight', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-line-height"> 
          {{ typoStore.typoState.lineHeight }} 
        </span> 
      </div> 
      <div class="typo-slider-row"> 
        <label>Word Spacing</label> 
        <input type="range" id="typo-word-spacing" 
          min="-2" max="8" step="0.5" 
          :value="typoStore.typoState.wordSpacing" 
          @input="applyTypoSpacing('wordSpacing', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-word-spacing"> 
          {{ typoStore.typoState.wordSpacing }}px 
        </span> 
      </div> 
      <div class="typo-slider-row"> 
        <label>Letter Spacing</label> 
        <input type="range" id="typo-letter-spacing" 
          min="-1" max="3" step="0.1" 
          :value="typoStore.typoState.letterSpacing" 
          @input="applyTypoSpacing('letterSpacing', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-letter-spacing"> 
          {{ typoStore.typoState.letterSpacing }}px 
        </span> 
      </div> 
      <div class="typo-slider-row"> 
        <label>Para Spacing</label> 
        <input type="range" id="typo-para-spacing" 
          min="0" max="20" step="1" 
          :value="typoStore.typoState.paraSpacing" 
          @input="applyTypoSpacing('paraSpacing', $event.target.value)"/> 
        <span class="typo-slider-val" id="val-para-spacing"> 
          {{ typoStore.typoState.paraSpacing }}px 
        </span> 
      </div> 

      <div class="typo-section-label"> 
        <i class="fa fa-bold"></i> Weight & Style 
      </div> 

      <div class="prop-group" style="margin-bottom:8px;"> 
        <label class="prop-label">Question Weight</label> 
        <select class="prop-select" id="typo-q-weight" 
          v-model="typoStore.typoState.qWeight" 
          @change="applyTypoWeight('q', $event.target.value)"> 
          <option value="400">Normal (400)</option> 
          <option value="500">Medium (500)</option> 
          <option value="600">Semi-Bold (600)</option> 
          <option value="700">Bold (700)</option> 
        </select> 
      </div> 
      <div class="prop-group" style="margin-bottom:8px;"> 
        <label class="prop-label">Section Weight</label> 
        <select class="prop-select" id="typo-section-weight" 
          v-model="typoStore.typoState.sectionWeight" 
          @change="applyTypoWeight('section', $event.target.value)"> 
          <option value="400">Normal (400)</option> 
          <option value="600">Semi-Bold (600)</option> 
          <option value="700">Bold (700)</option> 
          <option value="800">Extra Bold (800)</option> 
        </select> 
      </div> 

      <div style="display:flex;gap:6px;margin-top:12px;"> 
        <button class="btn-sm-dark" style="flex:1;" @click="resetTypography()"> 
          <i class="fa fa-undo"></i> Reset to Cambridge Default 
        </button> 
      </div> 

      <div style="background:rgba(91,138,240,0.08);border:1px solid 
        rgba(91,138,240,0.2);border-radius:6px;padding:8px 10px;margin-top:10px;"> 
        <div style="font-size:10px;font-weight:700;color:var(--accent-2); 
          margin-bottom:4px;"> 
          <i class="fa fa-info-circle"></i> Cambridge Typography Standards 
        </div> 
        <div style="font-size:9.5px;color:var(--text-muted);line-height:1.6;"> 
          <b style="color:var(--text-secondary);">O Level:</b> Arial 11pt, 
          Line Height 1.4–1.5, no extra spacing<br> 
          <b style="color:var(--text-secondary);">A Level:</b> Arial/Times 11pt, 
          Line Height 1.5, slight word spacing<br> 
          <b style="color:var(--text-secondary);">Pre-U:</b> Times New Roman 
          11–12pt, Line Height 1.5–1.6 
        </div> 
      </div> 
    </div>

    <!-- TAB 4: Style --> 
    <div class="rpanel-content" id="rtab-style" 
      v-show="uiStore.activeRTab === 'style'"> 
      <div class="prop-group"> 
        <label class="prop-label">Paper Size</label> 
        <select class="prop-select" @change="setPaperSize($event.target.value)"> 
          <option value="a4">A4 (210 × 297 mm)</option> 
          <option value="a3">A3 (297 × 420 mm)</option> 
          <option value="letter">Letter (8.5 × 11 in)</option> 
        </select> 
      </div> 
      <div class="prop-group"> 
        <label class="prop-label">Question Numbering</label> 
        <select class="prop-select" id="q-numbering" 
          v-model="examStore.qNumberStart" 
          @change="rerenderAll()"> 
          <option value="1">Start from 1</option> 
          <option value="auto">Automatic</option> 
        </select> 
      </div> 
      <div class="prop-group"> 
        <label class="prop-label">Options Layout</label> 
        <select class="prop-select" id="global-opts-layout" 
          v-model="examStore.globalOptsLayout" 
          @change="rerenderAll()"> 
          <option value="2col">2 Columns</option> 
          <option value="1col">1 Column</option> 
          <option value="inline">Inline</option> 
        </select> 
      </div> 
      <div class="prop-group"> 
        <label class="prop-label">Show Answer Boxes</label> 
        <div class="toggle-switch"> 
          <div class="toggle-track" 
            :class="{ on: examStore.showAnswerBoxes }" 
            @click="examStore.showAnswerBoxes = !examStore.showAnswerBoxes"> 
            <div class="toggle-thumb"></div> 
          </div> 
          <span class="toggle-label"> 
            Show blank answer box per question 
          </span> 
        </div> 
      </div> 
      <div class="prop-group" style="background:rgba(232,160,69,0.06); 
        border:1px solid rgba(232,160,69,0.2);border-radius:6px; 
        padding:8px 10px;"> 
        <div style="font-size:10px;color:var(--accent);font-weight:700; 
          margin-bottom:4px;"> 
          <i class="fa fa-info-circle"></i> Footer Settings 
        </div> 
        <div style="font-size:9.5px;color:var(--text-muted); 
          line-height:1.5;margin-bottom:6px;"> 
          Cover page aur content pages ke footers alag alag tabs mein hain: 
        </div> 
        <button class="btn-sm-dark" style="width:100%;margin-bottom:5px;" 
          @click="switchRTab('coverfooter')"> 
          <i class="fa fa-file-alt"></i> Cover Page Footer → 
        </button> 
        <button class="btn-sm-dark" style="width:100%;" 
          @click="switchRTab('pagefooter')"> 
          <i class="fa fa-copy"></i> Content Pages Footer → 
        </button> 
      </div> 
    </div> 

    <!-- TAB 5: Cover Footer --> 
    <div class="rpanel-content" id="rtab-coverfooter" 
      v-show="uiStore.activeRTab === 'coverfooter'"> 
      <!-- COVER FOOTER LEFT --> 
      <div class="prop-group"> 
        <label class="prop-label"> 
          <i class="fa fa-align-left" 
            style="color:var(--accent-2);margin-right:4px;"> 
          </i>Footer Left 
        </label> 
        <div class="mini-editor-wrap"> 
          <div class="mini-editor-toolbar"> 
            <button class="me-btn" 
              @mousedown.prevent="meCmdTarget('cover-footer-left','bold')"> 
              <b>B</b> 
            </button> 
            <button class="me-btn" 
              @mousedown.prevent="meCmdTarget('cover-footer-left','italic')"> 
              <i>I</i> 
            </button> 
            <button class="me-btn" 
              @mousedown.prevent="meCmdTarget('cover-footer-left','underline')"> 
              <u>U</u> 
            </button> 
          </div> 
          <div class="me-body" id="me-body-cover-footer-left" 
            contenteditable="true" 
            style="min-height:40px;max-height:80px;" 
            @input="updateCoverFooter()"> 
            <span v-html="examStore.coverFooter.left"></span>
          </div> 
        </div> 
      </div> 
      <!-- COVER FOOTER RIGHT --> 
      <div class="prop-group"> 
        <label class="prop-label"> 
          <i class="fa fa-align-right" 
            style="color:var(--accent-2);margin-right:4px;"> 
          </i>Footer Right Text 
        </label> 
        <input class="prop-input" id="cover-footer-right-inp" 
          v-model="examStore.coverFooter.right" placeholder="e.g. [Turn over]" 
          @input="updateCoverFooter()"/> 
      </div> 
      <!-- LOGO UPLOAD --> 
      <div class="prop-group"> 
        <label class="prop-label"> 
          <i class="fa fa-image" 
            style="color:var(--accent-2);margin-right:4px;"> 
          </i>Header Logo 
        </label> 
        <div class="img-upload-zone" 
          @click="$refs.logoUpload.click()"> 
          <i class="fa fa-cloud-upload-alt"></i> 
          Click to upload logo image 
        </div> 
        <input type="file" ref="logoUpload" accept="image/*" 
          style="display:none" @change="uploadLogo($event)"/> 
        <div class="prop-row" style="margin-top:8px;"> 
          <div> 
            <label class="prop-label">Logo Width (px)</label> 
            <input class="prop-input" type="number" 
              min="20" max="300" v-model="logoSize.width" 
              @input="updateLogoSize()"/> 
          </div> 
          <div> 
            <label class="prop-label">Logo Height (px)</label> 
            <input class="prop-input" type="number" 
              min="20" max="300" v-model="logoSize.height" 
              @input="updateLogoSize()"/> 
          </div> 
        </div> 
      </div> 
    </div> 

    <!-- TAB 6: Page Footer --> 
    <div class="rpanel-content" id="rtab-pagefooter" 
      v-show="uiStore.activeRTab === 'pagefooter'"> 
      <div style="background:rgba(91,138,240,0.08); 
        border:1px solid rgba(91,138,240,0.25); 
        border-radius:6px;padding:8px 10px;margin-bottom:12px;"> 
        <div style="font-size:10px;font-weight:700;color:var(--accent-2); 
          margin-bottom:3px;"> 
          <i class="fa fa-copy"></i> Content Pages Footer 
        </div> 
        <div style="font-size:9.5px;color:var(--text-muted);line-height:1.5;"> 
          Page 2 se aage saare content pages par apply hongi. 
        </div> 
      </div> 
      <!-- PAGE FOOTER LEFT --> 
      <div class="prop-group"> 
        <label class="prop-label"> 
          <i class="fa fa-align-left" 
            style="color:var(--accent-2);margin-right:4px;"> 
          </i>Footer Left 
        </label> 
        <div class="mini-editor-wrap"> 
          <div class="mini-editor-toolbar"> 
            <button class="me-btn" 
              @mousedown.prevent="meCmdTarget('page-footer-left','bold')"> 
              <b>B</b> 
            </button> 
            <button class="me-btn" 
              @mousedown.prevent="meCmdTarget('page-footer-left','italic')"> 
              <i>I</i> 
            </button> 
          </div> 
          <div class="me-body" id="me-body-page-footer-left" 
            contenteditable="true" 
            style="min-height:40px;max-height:80px;" 
            @input="updatePageFooterSettings()"> 
            <span v-html="examStore.pageFooter.left"></span>
          </div> 
        </div> 
      </div> 
      <!-- PAGE FOOTER RIGHT --> 
      <div class="prop-group"> 
        <label class="prop-label">Footer Right</label> 
        <input class="prop-input" id="page-footer-right-inp" 
          v-model="examStore.pageFooter.right" 
          @input="updatePageFooterSettings()"/> 
      </div> 
      <!-- SHOW PAGE NUMBER --> 
      <div class="prop-group"> 
        <label class="prop-label">Show Page Number in Center</label> 
        <div class="toggle-switch"> 
          <div class="toggle-track" 
            :class="{ on: uiStore.footerShowPageNum }" 
            id="toggle-footer-pagenum" 
            @click="toggleFooterPageNum()"> 
            <div class="toggle-thumb"></div> 
          </div> 
          <span class="toggle-label">Show page number centered</span> 
        </div> 
      </div> 
    </div> 
  </div>
</template>

<script setup>
import { computed, ref, reactive } from 'vue';
import { useExamStore } from '../stores/examStore';
import { useUiStore } from '../stores/uiStore';
import { useTypoStore } from '../stores/typoStore';
import { useTypography } from '../composables/useTypography';
import { usePrint } from '../composables/usePrint';
import PropertiesPanel from './PropertiesPanel.vue';

const examStore = useExamStore();
const uiStore = useUiStore();
const typoStore = useTypoStore();
const { 
  applyTypoPreset, 
  applyTypoFont, 
  applyTypoSize, 
  applyTypoSpacing, 
  applyTypoWeight, 
  resetTypography 
} = useTypography();
const { exportAnswerKey } = usePrint();

const tabLabels = {
  properties: 'Properties',
  answerkey: 'Answer Key',
  typography: 'Typography',
  style: 'Style',
  coverfooter: 'Cover Footer',
  pagefooter: 'Page Footer'
};

const logoSize = reactive({ width: 70, height: 50 });

function switchRTab(tab) {
  uiStore.activeRTab = tab;
}

function togglePanel(side) {
  if (side === 'right') uiStore.rightCollapsed = !uiStore.rightCollapsed;
}

function setPaperSize(val) {
  examStore.styleState.pageSize = val;
}

function rerenderAll() {
  // Logic to trigger a global re-render or re-calculation if needed
}

function meCmdTarget(targetId, cmd) {
  document.execCommand(cmd, false, null);
}

function updateCoverFooter() {
  const el = document.getElementById('me-body-cover-footer-left');
  if (el) examStore.coverFooter.left = el.innerHTML;
}

function updatePageFooterSettings() {
  const el = document.getElementById('me-body-page-footer-left');
  if (el) examStore.pageFooter.left = el.innerHTML;
}

function toggleFooterPageNum() {
  uiStore.footerShowPageNum = !uiStore.footerShowPageNum;
}

function uploadLogo(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      examStore.paperMeta.logo = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function updateLogoSize() {
  // Can be used to inject CSS or store size
}

const allMcqBlocks = computed(() => {
  const blocks = [];
  examStore.pages.forEach(page => {
    page.blocks.forEach(block => {
      if (block.type === 'mcq') blocks.push(block);
    });
  });
  return blocks;
});
</script>
