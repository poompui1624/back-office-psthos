<script>
    const fiscalYearSelect = document.getElementById('fiscal_year_id');
    const mainTopicSelect = document.getElementById('main_topic_id');
    const subTopicSelect = document.getElementById('sub_topic_id');

    function filterMainTopicsByYear() {
        const selectedYear = fiscalYearSelect.value;

        Array.from(mainTopicSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.year !== selectedYear;
        });

        if (
            mainTopicSelect.selectedOptions.length &&
            mainTopicSelect.selectedOptions[0].hidden
        ) {
            mainTopicSelect.value = '';
            subTopicSelect.innerHTML = '<option value="">-- เลือกหัวข้อย่อย --</option>';
        }
    }

    async function loadSubTopics() {
        const fiscalYearId = fiscalYearSelect.value;
        const mainTopicId = mainTopicSelect.value;

        subTopicSelect.innerHTML = '<option value="">-- กำลังโหลดหัวข้อย่อย --</option>';

        if (!fiscalYearId || !mainTopicId) {
            subTopicSelect.innerHTML = '<option value="">-- เลือกหัวข้อย่อย --</option>';
            return;
        }

        const url = new URL('{{ route('ita.sub-topics') }}', window.location.origin);
        url.searchParams.set('fiscal_year_id', fiscalYearId);
        url.searchParams.set('main_topic_id', mainTopicId);

        const response = await fetch(url);
        const data = await response.json();

        subTopicSelect.innerHTML = '<option value="">-- เลือกหัวข้อย่อย --</option>';

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.code} ${item.title}`;
            subTopicSelect.appendChild(option);
        });
    }

    fiscalYearSelect.addEventListener('change', function () {
        filterMainTopicsByYear();
        loadSubTopics();
    });

    mainTopicSelect.addEventListener('change', loadSubTopics);

    filterMainTopicsByYear();
</script>
