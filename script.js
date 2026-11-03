/**
 * Cascading Dropdown Handler for Survey IPT System
 * Handles AJAX requests, dependent selection logic & interactive skill choices
 */

document.addEventListener('DOMContentLoaded', () => {
    // UI Elements
    const form = document.getElementById('surveyForm');
    const nameInput = document.getElementById('respondent_name');
    const dropState = document.getElementById('drop_state');
    const dropInstitute = document.getElementById('drop_institute');
    const dropCourse = document.getElementById('drop_course');
    const submitBtn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('alertBox');

    // Skills Elements
    const skillsContainer = document.getElementById('skillsContainer');
    const techSkillsWrapper = document.getElementById('techSkillsWrapper');
    const softSkillsWrapper = document.getElementById('softSkillsWrapper');
    const techSearch = document.getElementById('techSearch');

    // Live Summary Elements
    const sumState = document.getElementById('sum_state');
    const sumInstitute = document.getElementById('sum_institute');
    const sumCourse = document.getElementById('sum_course');
    const sumSkills = document.getElementById('sum_skills');

    let selectedSkillIds = new Set();
    let coursesDataMap = new Map(); // Store course info for quick lookup

    if (!dropState) return; // Not on survey page

    // Load Initial States
    loadStates();

    // 1. On State Selected
    dropState.addEventListener('change', () => {
        const stateId = dropState.value;
        const stateText = dropState.options[dropState.selectedIndex]?.text || '-';

        sumState.textContent = stateId ? stateText : '- Belum Dipilih -';
        
        resetDropdown(dropInstitute, 'Pilih Institusi...');
        resetDropdown(dropCourse, 'Pilih Kursus...');
        hideSkills();
        
        sumInstitute.textContent = '- Belum Dipilih -';
        sumCourse.textContent = '- Belum Dipilih -';

        validateForm();

        if (stateId) {
            loadInstitutes(stateId);
        }
    });

    // 2. On Institute Selected
    dropInstitute.addEventListener('change', () => {
        const instituteId = dropInstitute.value;
        const instText = dropInstitute.options[dropInstitute.selectedIndex]?.text || '-';

        sumInstitute.textContent = instituteId ? instText : '- Belum Dipilih -';

        resetDropdown(dropCourse, 'Pilih Kursus...');
        hideSkills();
        
        sumCourse.textContent = '- Belum Dipilih -';

        validateForm();

        if (instituteId) {
            loadCourses(instituteId);
        }
    });

    // 3. On Course Selected
    dropCourse.addEventListener('change', () => {
        const courseId = dropCourse.value;
        const courseText = dropCourse.options[dropCourse.selectedIndex]?.text || '-';

        sumCourse.textContent = courseId ? courseText : '- Belum Dipilih -';

        validateForm();

        if (courseId) {
            loadSkills(courseId);
        } else {
            hideSkills();
        }
    });

    nameInput.addEventListener('input', validateForm);

    // Live Technical Skill Search
    if (techSearch) {
        techSearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const chips = techSkillsWrapper.querySelectorAll('.skill-chip');
            chips.forEach(chip => {
                const text = chip.getAttribute('data-name').toLowerCase();
                chip.style.display = text.includes(query) ? 'inline-flex' : 'none';
            });
        });
    }

    // ========================================================
    // AJAX FETCH FUNCTIONS
    // ========================================================

    function loadStates() {
        showSpinner(dropState, true);
        fetch('api.php?action=get_states')
            .then(res => res.json())
            .then(data => {
                showSpinner(dropState, false);
                if (data.status === 'success') {
                    populateOptions(dropState, data.data, 'id', 'state_name', 'Pilih Negeri...');
                    dropState.disabled = false;
                } else {
                    showAlert(data.message || 'Gagal memuatkan senarai negeri.', 'error');
                }
            })
            .catch(err => {
                showSpinner(dropState, false);
                showAlert('Ralat sambungan database. Pastikan XAMPP MySQL telah dijalankan.', 'error');
            });
    }

    function loadInstitutes(stateId) {
        showSpinner(dropInstitute, true);
        fetch(`api.php?action=get_institutes&state_id=${stateId}`)
            .then(res => res.json())
            .then(data => {
                showSpinner(dropInstitute, false);
                if (data.status === 'success') {
                    if (data.data.length === 0) {
                        resetDropdown(dropInstitute, 'Tiada Institusi Dijumpai');
                    } else {
                        populateOptions(dropInstitute, data.data, 'id', 'institute_name', 'Pilih Institusi...');
                        dropInstitute.disabled = false;
                    }
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(() => {
                showSpinner(dropInstitute, false);
                showAlert('Gagal memuatkan institusi.', 'error');
            });
    }

    function loadCourses(instituteId) {
        showSpinner(dropCourse, true);
        fetch(`api.php?action=get_courses&institute_id=${instituteId}`)
            .then(res => res.json())
            .then(data => {
                showSpinner(dropCourse, false);
                if (data.status === 'success') {
                    if (data.data.length === 0) {
                        resetDropdown(dropCourse, 'Tiada Kursus Ditawarkan');
                    } else {
                        coursesDataMap.clear();
                        data.data.forEach(c => coursesDataMap.set(c.id.toString(), c));
                        populateOptions(dropCourse, data.data, 'id', 'course_name', 'Pilih Kursus Ditawarkan...');
                        dropCourse.disabled = false;
                    }
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(() => {
                showSpinner(dropCourse, false);
                showAlert('Gagal memuatkan kursus.', 'error');
            });
    }

    function loadSkills(courseId) {
        fetch(`api.php?action=get_skills&course_id=${courseId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const techList = res.data.technical || [];
                    const softList = res.data.soft || [];

                    selectedSkillIds.clear();
                    updateSkillsSummary();

                    if (techList.length > 0 || softList.length > 0) {
                        renderSkillChips(techSkillsWrapper, techList, 'tech');
                        renderSkillChips(softSkillsWrapper, softList, 'soft');
                        skillsContainer.style.display = 'block';
                    } else {
                        hideSkills();
                    }
                }
            })
            .catch(() => hideSkills());
    }

    function renderSkillChips(container, skills, type) {
        container.innerHTML = '';
        skills.forEach(s => {
            const label = document.createElement('label');
            label.className = 'skill-chip';
            label.setAttribute('data-id', s.id);
            label.setAttribute('data-name', s.skill_name);

            const icon = type === 'tech' ? 'fa-wrench' : 'fa-brain';

            label.innerHTML = `
                <i class="fa-solid ${icon} skill-chip-icon"></i>
                <span>${escapeHtml(s.skill_name)}</span>
            `;

            label.addEventListener('click', () => {
                if (selectedSkillIds.has(s.id)) {
                    selectedSkillIds.delete(s.id);
                    label.classList.remove('selected');
                } else {
                    selectedSkillIds.add(s.id);
                    label.classList.add('selected');
                }
                updateSkillsSummary();
            });

            container.appendChild(label);
        });
    }

    function updateSkillsSummary() {
        if (sumSkills) {
            sumSkills.textContent = `${selectedSkillIds.size} Kemahiran Dipilih`;
        }
    }

    function hideSkills() {
        if (skillsContainer) skillsContainer.style.display = 'none';
        selectedSkillIds.clear();
        updateSkillsSummary();
    }

    // Form Submission AJAX
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'submit_survey');

        // Append selected skills
        selectedSkillIds.forEach(id => {
            formData.append('skills[]', id);
        });

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menghantar...';

        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.textContent = 'Hantar Maklumat Tinjauan';
            if (data.status === 'success') {
                showAlert(data.message, 'success');
                form.reset();
                resetDropdown(dropInstitute, 'Pilih Institusi...');
                resetDropdown(dropCourse, 'Pilih Kursus...');
                hideSkills();
                sumState.textContent = '- Belum Dipilih -';
                sumInstitute.textContent = '- Belum Dipilih -';
                sumCourse.textContent = '- Belum Dipilih -';
                validateForm();
            } else {
                showAlert(data.message, 'error');
                validateForm();
            }
        })
        .catch(err => {
            submitBtn.textContent = 'Hantar Maklumat Tinjauan';
            showAlert('Ralat semasa menghantar borang.', 'error');
            validateForm();
        });
    });

    // Helper Functions
    function populateOptions(selectElem, items, valueKey, textKey, placeholder) {
        selectElem.innerHTML = `<option value="">-- ${placeholder} --</option>`;
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[textKey];
            selectElem.appendChild(opt);
        });
    }

    function resetDropdown(selectElem, placeholder) {
        selectElem.innerHTML = `<option value="">-- ${placeholder} --</option>`;
        selectElem.disabled = true;
    }

    function showSpinner(selectElem, show) {
        const wrapper = selectElem.closest('.select-wrapper');
        if (!wrapper) return;
        const spinner = wrapper.querySelector('.spinner-inline');
        const arrow = wrapper.querySelector('.select-arrow');
        if (spinner && arrow) {
            spinner.style.display = show ? 'block' : 'none';
            arrow.style.display = show ? 'none' : 'block';
        }
    }

    function validateForm() {
        const isNameFilled = nameInput.value.trim().length > 0;
        const isStateSelected = !!dropState.value;
        const isInstSelected = !!dropInstitute.value;
        const isCourseSelected = !!dropCourse.value;

        submitBtn.disabled = !(isNameFilled && isStateSelected && isInstSelected && isCourseSelected);
    }

    function showAlert(msg, type) {
        alertBox.className = `alert-box ${type}`;
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ========================================================
    // SUGGEST NEW INSTITUTE MODAL (NORMAL USER)
    // ========================================================
    const btnOpenSuggestModal = document.getElementById('btnOpenSuggestModal');
    const modalSuggestInstitute = document.getElementById('modalSuggestInstitute');
    const btnCloseSuggestModal = document.getElementById('btnCloseSuggestModal');
    const btnCancelSuggestModal = document.getElementById('btnCancelSuggestModal');
    const modalSuggestForm = document.getElementById('modalSuggestForm');
    const modalSuggestState = document.getElementById('modal_suggest_state_id');
    const modalSuggestName = document.getElementById('modal_suggest_institute_name');
    const modalSuggestAlert = document.getElementById('modalSuggestAlert');
    const btnSubmitModalSuggest = document.getElementById('btnSubmitModalSuggest');

    function showModalSuggestAlert(msg, type = 'info') {
        if (!modalSuggestAlert) return;
        modalSuggestAlert.style.display = 'block';
        modalSuggestAlert.className = `alert-box alert-${type}`;
        modalSuggestAlert.innerHTML = `<span>${escapeHtml(msg)}</span>`;
    }

    if (btnOpenSuggestModal && modalSuggestInstitute) {
        btnOpenSuggestModal.addEventListener('click', () => {
            // Populate states into modal select if empty
            if (modalSuggestState && modalSuggestState.options.length <= 1) {
                Array.from(dropState.options).forEach(opt => {
                    if (opt.value) {
                        const newOpt = document.createElement('option');
                        newOpt.value = opt.value;
                        newOpt.textContent = opt.textContent;
                        modalSuggestState.appendChild(newOpt);
                    }
                });
            }

            // Sync currently selected state from survey form if set
            if (dropState && dropState.value && modalSuggestState) {
                modalSuggestState.value = dropState.value;
            }

            if (modalSuggestName) modalSuggestName.value = '';
            if (modalSuggestAlert) modalSuggestAlert.style.display = 'none';

            modalSuggestInstitute.classList.add('active');
        });

        const closeSuggestModal = () => {
            modalSuggestInstitute.classList.remove('active');
        };

        if (btnCloseSuggestModal) btnCloseSuggestModal.addEventListener('click', closeSuggestModal);
        if (btnCancelSuggestModal) btnCancelSuggestModal.addEventListener('click', closeSuggestModal);

        modalSuggestInstitute.addEventListener('click', (e) => {
            if (e.target === modalSuggestInstitute) {
                closeSuggestModal();
            }
        });

        if (modalSuggestForm) {
            modalSuggestForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const stateId = modalSuggestState.value;
                const instName = modalSuggestName.value.trim();

                if (!stateId || !instName) {
                    showModalSuggestAlert('Sila pilih negeri dan masukkan nama institusi.', 'error');
                    return;
                }

                btnSubmitModalSuggest.disabled = true;
                btnSubmitModalSuggest.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghantar...';

                const fd = new FormData();
                fd.append('action', 'suggest_institute');
                fd.append('state_id', stateId);
                fd.append('institute_name', instName);

                fetch('api.php', {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(res => {
                    btnSubmitModalSuggest.disabled = false;
                    btnSubmitModalSuggest.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan';

                    if (res.status === 'success') {
                        showModalSuggestAlert(res.message, 'success');
                        modalSuggestName.value = '';
                        setTimeout(() => {
                            closeSuggestModal();
                            showAlert('Cadangan institusi anda telah dihantar kepada pentadbir sistem untuk semakan.', 'success');
                        }, 1800);
                    } else if (res.status === 'warning') {
                        showModalSuggestAlert(res.message, 'warning');
                    } else if (res.status === 'info') {
                        showModalSuggestAlert(res.message, 'info');
                    } else {
                        showModalSuggestAlert(res.message || 'Ralat semasa menghantar cadangan.', 'error');
                    }
                })
                .catch(err => {
                    btnSubmitModalSuggest.disabled = false;
                    btnSubmitModalSuggest.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan';
                    showModalSuggestAlert('Ralat sambungan: ' + err.message, 'error');
                });
            });
        }
    }

    // ========================================================
    // SUGGEST NEW COURSE MODAL (NORMAL USER)
    // ========================================================
    const btnOpenSuggestCourseModal = document.getElementById('btnOpenSuggestCourseModal');
    const modalSuggestCourse = document.getElementById('modalSuggestCourse');
    const btnCloseSuggestCourseModal = document.getElementById('btnCloseSuggestCourseModal');
    const btnCancelSuggestCourseModal = document.getElementById('btnCancelSuggestCourseModal');
    const modalSuggestCourseForm = document.getElementById('modalSuggestCourseForm');
    const modalSuggestCourseState = document.getElementById('modal_suggest_course_state_id');
    const modalSuggestCourseInst = document.getElementById('modal_suggest_course_institute_id');
    const modalSuggestCourseName = document.getElementById('modal_suggest_course_name');
    const modalSuggestCourseAlert = document.getElementById('modalSuggestCourseAlert');
    const btnSubmitModalSuggestCourse = document.getElementById('btnSubmitModalSuggestCourse');

    function showModalSuggestCourseAlert(msg, type = 'info') {
        if (!modalSuggestCourseAlert) return;
        modalSuggestCourseAlert.style.display = 'block';
        modalSuggestCourseAlert.className = `alert-box alert-${type}`;
        modalSuggestCourseAlert.innerHTML = `<span>${escapeHtml(msg)}</span>`;
    }

    function loadModalInstitutes(stateId, preselectedInstId = '') {
        if (!modalSuggestCourseInst) return;
        modalSuggestCourseInst.innerHTML = '<option value="">-- Memuatkan Institusi... --</option>';
        modalSuggestCourseInst.disabled = true;

        if (!stateId) {
            modalSuggestCourseInst.innerHTML = '<option value="">-- Sila Pilih Negeri Terlebih Dahulu --</option>';
            return;
        }

        fetch(`api.php?action=get_institutes&state_id=${stateId}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success' && res.data) {
                    if (res.data.length === 0) {
                        modalSuggestCourseInst.innerHTML = '<option value="">-- Tiada IPT Dijumpai --</option>';
                        modalSuggestCourseInst.disabled = true;
                    } else {
                        modalSuggestCourseInst.innerHTML = '<option value="">-- Pilih Institusi (IPT) --</option>';
                        res.data.forEach(inst => {
                            const opt = document.createElement('option');
                            opt.value = inst.id;
                            opt.textContent = inst.institute_name;
                            if (preselectedInstId && String(inst.id) === String(preselectedInstId)) {
                                opt.selected = true;
                            }
                            modalSuggestCourseInst.appendChild(opt);
                        });
                        modalSuggestCourseInst.disabled = false;
                    }
                }
            })
            .catch(() => {
                modalSuggestCourseInst.innerHTML = '<option value="">-- Gagal memuatkan institusi --</option>';
            });
    }

    if (btnOpenSuggestCourseModal && modalSuggestCourse) {
        btnOpenSuggestCourseModal.addEventListener('click', () => {
            // Populate states into modal select if empty
            if (modalSuggestCourseState && modalSuggestCourseState.options.length <= 1) {
                Array.from(dropState.options).forEach(opt => {
                    if (opt.value) {
                        const newOpt = document.createElement('option');
                        newOpt.value = opt.value;
                        newOpt.textContent = opt.textContent;
                        modalSuggestCourseState.appendChild(newOpt);
                    }
                });
            }

            // Sync currently selected state & institute from survey form if set
            const activeStateId = dropState ? dropState.value : '';
            const activeInstId = dropInstitute ? dropInstitute.value : '';

            if (modalSuggestCourseState && activeStateId) {
                modalSuggestCourseState.value = activeStateId;
                loadModalInstitutes(activeStateId, activeInstId);
            } else if (modalSuggestCourseInst) {
                modalSuggestCourseInst.innerHTML = '<option value="">-- Sila Pilih Negeri Terlebih Dahulu --</option>';
                modalSuggestCourseInst.disabled = true;
            }

            if (modalSuggestCourseName) modalSuggestCourseName.value = '';
            if (modalSuggestCourseAlert) modalSuggestCourseAlert.style.display = 'none';

            modalSuggestCourse.classList.add('active');
        });

        if (modalSuggestCourseState) {
            modalSuggestCourseState.addEventListener('change', () => {
                loadModalInstitutes(modalSuggestCourseState.value);
            });
        }

        const closeSuggestCourseModal = () => {
            modalSuggestCourse.classList.remove('active');
        };

        if (btnCloseSuggestCourseModal) btnCloseSuggestCourseModal.addEventListener('click', closeSuggestCourseModal);
        if (btnCancelSuggestCourseModal) btnCancelSuggestCourseModal.addEventListener('click', closeSuggestCourseModal);

        modalSuggestCourse.addEventListener('click', (e) => {
            if (e.target === modalSuggestCourse) {
                closeSuggestCourseModal();
            }
        });

        if (modalSuggestCourseForm) {
            modalSuggestCourseForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const instId = modalSuggestCourseInst.value;
                const courseName = modalSuggestCourseName.value.trim();
                const qualLevel = document.getElementById('modal_suggest_qualification_level').value;
                const branch = document.getElementById('modal_suggest_campus_branch').value.trim() || 'Kampus Utama';

                if (!instId || !courseName) {
                    showModalSuggestCourseAlert('Sila pilih institusi dan masukkan nama kursus.', 'error');
                    return;
                }

                btnSubmitModalSuggestCourse.disabled = true;
                btnSubmitModalSuggestCourse.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghantar...';

                const fd = new FormData();
                fd.append('action', 'suggest_course');
                fd.append('institute_id', instId);
                fd.append('course_name', courseName);
                fd.append('qualification_level', qualLevel);
                fd.append('campus_branch', branch);

                fetch('api.php', {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(res => {
                    btnSubmitModalSuggestCourse.disabled = false;
                    btnSubmitModalSuggestCourse.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Kursus';

                    if (res.status === 'success') {
                        showModalSuggestCourseAlert(res.message, 'success');
                        modalSuggestCourseName.value = '';
                        setTimeout(() => {
                            closeSuggestCourseModal();
                            showAlert('Cadangan kursus anda telah dihantar kepada pentadbir sistem untuk semakan.', 'success');
                        }, 1800);
                    } else if (res.status === 'warning') {
                        showModalSuggestCourseAlert(res.message, 'warning');
                    } else if (res.status === 'info') {
                        showModalSuggestCourseAlert(res.message, 'info');
                    } else {
                        showModalSuggestCourseAlert(res.message || 'Ralat semasa menghantar cadangan.', 'error');
                    }
                })
                .catch(err => {
                    btnSubmitModalSuggestCourse.disabled = false;
                    btnSubmitModalSuggestCourse.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Kursus';
                    showModalSuggestCourseAlert('Ralat sambungan: ' + err.message, 'error');
                });
            });
        }
    }

    // ========================================================
    // SUGGEST NEW SKILL MODAL (NORMAL USER)
    // ========================================================
    const btnOpenSuggestSkillModal = document.getElementById('btnOpenSuggestSkillModal');
    const modalSuggestSkill = document.getElementById('modalSuggestSkill');
    const btnCloseSuggestSkillModal = document.getElementById('btnCloseSuggestSkillModal');
    const btnCancelSuggestSkillModal = document.getElementById('btnCancelSuggestSkillModal');
    const modalSuggestSkillForm = document.getElementById('modalSuggestSkillForm');
    const modalSuggestSkillName = document.getElementById('modal_suggest_skill_name');
    const modalSuggestSkillType = document.getElementById('modal_suggest_skill_type');
    const modalSuggestSkillQual = document.getElementById('modal_suggest_skill_qualification');
    const modalSuggestSkillDesc = document.getElementById('modal_suggest_skill_description');
    const modalSuggestSkillAlert = document.getElementById('modalSuggestSkillAlert');
    const btnSubmitModalSuggestSkill = document.getElementById('btnSubmitModalSuggestSkill');

    function showModalSuggestSkillAlert(msg, type = 'info') {
        if (!modalSuggestSkillAlert) return;
        modalSuggestSkillAlert.style.display = 'block';
        modalSuggestSkillAlert.className = `alert-box alert-${type}`;
        modalSuggestSkillAlert.innerHTML = `<span>${escapeHtml(msg)}</span>`;
    }

    if (btnOpenSuggestSkillModal && modalSuggestSkill) {
        btnOpenSuggestSkillModal.addEventListener('click', () => {
            if (modalSuggestSkillName) modalSuggestSkillName.value = '';
            if (modalSuggestSkillType) modalSuggestSkillType.value = '';
            if (modalSuggestSkillQual) modalSuggestSkillQual.value = 'Diploma';
            if (modalSuggestSkillDesc) modalSuggestSkillDesc.value = '';
            if (modalSuggestSkillAlert) modalSuggestSkillAlert.style.display = 'none';

            modalSuggestSkill.classList.add('active');
        });

        const closeSuggestSkillModal = () => {
            modalSuggestSkill.classList.remove('active');
        };

        if (btnCloseSuggestSkillModal) btnCloseSuggestSkillModal.addEventListener('click', closeSuggestSkillModal);
        if (btnCancelSuggestSkillModal) btnCancelSuggestSkillModal.addEventListener('click', closeSuggestSkillModal);

        modalSuggestSkill.addEventListener('click', (e) => {
            if (e.target === modalSuggestSkill) {
                closeSuggestSkillModal();
            }
        });

        if (modalSuggestSkillForm) {
            modalSuggestSkillForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const skillName = modalSuggestSkillName.value.trim();
                const skillType = modalSuggestSkillType.value;
                const qualLevel = modalSuggestSkillQual ? modalSuggestSkillQual.value : 'Diploma';
                const skillDesc = modalSuggestSkillDesc ? modalSuggestSkillDesc.value.trim() : '';

                if (!skillName || !skillType) {
                    showModalSuggestSkillAlert('Sila masukkan nama kemahiran dan pilih jenis kemahiran.', 'error');
                    return;
                }

                btnSubmitModalSuggestSkill.disabled = true;
                btnSubmitModalSuggestSkill.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghantar...';

                const fd = new FormData();
                fd.append('action', 'suggest_skill');
                fd.append('skill_name', skillName);
                fd.append('skill_type', skillType);
                fd.append('qualification_level', qualLevel);
                fd.append('skill_description', skillDesc);

                fetch('api.php', {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(res => {
                    btnSubmitModalSuggestSkill.disabled = false;
                    btnSubmitModalSuggestSkill.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Kemahiran';

                    if (res.status === 'success') {
                        showModalSuggestSkillAlert(res.message, 'success');
                        modalSuggestSkillName.value = '';
                        if (modalSuggestSkillDesc) modalSuggestSkillDesc.value = '';
                        setTimeout(() => {
                            closeSuggestSkillModal();
                            showAlert('Cadangan kemahiran anda telah dihantar kepada pentadbir sistem untuk semakan.', 'success');
                        }, 1800);
                    } else if (res.status === 'warning') {
                        showModalSuggestSkillAlert(res.message, 'warning');
                    } else if (res.status === 'info') {
                        showModalSuggestSkillAlert(res.message, 'info');
                    } else {
                        showModalSuggestSkillAlert(res.message || 'Ralat semasa menghantar cadangan.', 'error');
                    }
                })
                .catch(err => {
                    btnSubmitModalSuggestSkill.disabled = false;
                    btnSubmitModalSuggestSkill.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Kemahiran';
                    showModalSuggestSkillAlert('Ralat sambungan: ' + err.message, 'error');
                });
            });
        }
    }
});

