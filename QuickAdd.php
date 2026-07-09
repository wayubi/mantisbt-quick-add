<?php

class QuickAddPlugin extends MantisPlugin {
    function register() {
        $this->name = plugin_lang_get('title');
        $this->description = plugin_lang_get('description');
        $this->version = '1.0';
        $this->requires = array(
            'MantisCore' => '2.0.0',
        );
        $this->author = 'W. Latif Ayubi';
    }

    function hooks() {
        return array(
            'EVENT_LAYOUT_RESOURCES' => 'resources',
            'EVENT_LAYOUT_BODY_END' => 'modal_html',
        );
    }

    function resources() {
        if (!auth_is_user_authenticated()) {
            return '';
        }

        return '<script src="' . plugin_file('quick_add.js') . '"></script>';
    }

    function modal_html() {
        if (!auth_is_user_authenticated()) {
            return;
        }

        $t_user_id = auth_get_current_user_id();
        $t_project_ids = user_get_accessible_projects($t_user_id);

        $t_default_project_id = 0;

        $t_page = basename( $_SERVER['SCRIPT_NAME'] );
        if( $t_page === 'my_view_page.php' ) {
            $t_preferred = 1;
        } else {
            $t_preferred = helper_get_current_project();
        }

        if( $t_preferred > 0
            && access_has_project_level(
                config_get( 'report_bug_threshold', null, $t_user_id, $t_preferred ),
                $t_preferred, $t_user_id
            )
        ) {
            $t_default_project_id = $t_preferred;
        }

        if( $t_default_project_id === 0 ) {
            foreach( $t_project_ids as $t_id ) {
                if( access_has_project_level(
                    config_get( 'report_bug_threshold', null, $t_user_id, $t_id ),
                    $t_id, $t_user_id
                ) ) {
                    $t_default_project_id = $t_id;
                    break;
                }
            }
        }

        if( $t_default_project_id === 0 ) {
            return;
        }

        ob_start();
        print_project_option_list($t_default_project_id, false, null, false, true);
        $t_project_options = ob_get_clean();

        $t_form_action = plugin_page('quick_add');
        $t_today = date('Y-m-d');
        $t_security_token = form_security_field('quick_add');

        $t_ajax_url = plugin_page('quick_add_ajax');
        return '
<style>
.quick-add-fab {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    top: auto !important;
    left: auto !important;
    z-index: 1050;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    font-size: 28px;
    line-height: 1;
    padding: 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    display: block;
    touch-action: manipulation;
}
.quick-add-fab:active,
.quick-add-fab:focus,
.quick-add-fab:hover {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    top: auto !important;
    left: auto !important;
    transform: none !important;
}
#quick-add-form .form-control {
    width: 100% !important;
}
#quick-add-form .modal-footer button,
#quick-add-form .modal-footer input {
  height: auto;
}
</style>
<button type="button" class="quick-add-fab btn btn-primary" title="' . plugin_lang_get('title') . '" data-toggle="modal" data-target="#quick-add-modal">+</button>
<div class="modal fade" id="quick-add-modal" tabindex="-1" role="dialog" data-quick-add-ajax-url="' . $t_ajax_url . '">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">' . plugin_lang_get('modal_title') . '</h4>
      </div>
      <form method="post" action="' . $t_form_action . '" id="quick-add-form">
        ' . $t_security_token . '
        <div class="modal-body">
          <div class="form-group">
            <label for="quick-add-project">' . lang_get('project_name') . '</label>
            <select id="quick-add-project" name="project_id" class="form-control input-sm">
              ' . $t_project_options . '
            </select>
          </div>
          <div class="form-group">
            <label for="quick-add-category"><span class="required">*</span> ' . lang_get('category') . '</label>
            <select id="quick-add-category" name="category_id" class="form-control input-sm" required>
              <option value="">' . lang_get('select_option') . '</option>
            </select>
          </div>
          <div class="form-group">
            <label for="quick-add-summary"><span class="required">*</span> ' . lang_get('summary') . '</label>
            <input type="text" id="quick-add-summary" name="summary" class="form-control input-sm" maxlength="128" required autocomplete="off" />
          </div>
          <div class="form-group">
            <label for="quick-add-description">' . lang_get('description') . '</label>
            <textarea id="quick-add-description" name="description" class="form-control input-sm" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label>' . lang_get('due_date') . '</label>
            <div class="row">
              <div class="col-xs-6">
                <input type="date" id="quick-add-due-date" name="due_date" class="form-control input-sm" value="' . $t_today . '" />
              </div>
              <div class="col-xs-6">
                <input type="time" id="quick-add-due-time" name="due_time" class="form-control input-sm" value="12:00" />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">' . plugin_lang_get('cancel') . '</button>
          <input type="submit" name="submit" value="' . plugin_lang_get('submit_button') . '" class="btn btn-primary btn-sm" />
        </div>
      </form>
    </div>
  </div>
</div>';
    }
}
