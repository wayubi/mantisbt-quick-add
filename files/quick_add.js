(function($) {
    'use strict';

    function getAjaxUrl() {
        return $('#quick-add-modal').data('quick-add-ajax-url');
    }

    function loadCategories(projectId) {
        var url = getAjaxUrl();
        if (!url) return;
        $.get(url + '&project_id=' + projectId, function(data) {
            var select = $('#quick-add-category');
            select.empty();
            if (data.categories && data.categories.length > 0) {
                $.each(data.categories, function(i, cat) {
                    select.append($('<option>', {
                        value: cat.id,
                        text: cat.name
                    }));
                });
            } else {
                select.append($('<option>', {
                    value: '',
                    text: '-- No categories --'
                }));
            }
        });
    }

    $(document).on('change', '#quick-add-project', function() {
        loadCategories($(this).val());
    });

    $(document).on('shown.bs.modal', '#quick-add-modal', function() {
        $('#quick-add-summary').focus();
    });

    $(document).on('keydown', function(e) {
        if (e.which !== 81 || e.ctrlKey || e.metaKey || e.altKey) {
            return;
        }
        var tag = e.target.tagName.toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select') {
            return;
        }
        e.preventDefault();
        $('#quick-add-modal').modal('show');
    });

    $(function() {
        var projectVal = $('#quick-add-project').val();
        if (projectVal) {
            loadCategories(projectVal);
        }
    });

})(jQuery);
