<?php

class Proposal
{

    public
        $current_user_id = NULL,
        $userdata = NULL,
        $current_user_meta = NULL,
        $post_data = NULL,
        $name = NULL,
        $status = NULL,
        $title = NULL,
        $task_number = NULL,
        $cmrg_group = NULL,
        $ctrg = NULL,
        $period = NULL,
        $continuing_task = NULL,
        $task_related_to = NULL,
        $science_objective = NULL,
        $science_objective_importance = NULL,
        $state_of_art_capabilities = NULL,
        $summary_of_research_strategy = NULL,
        $extended_tech_description = NULL,
        $progress_by_mach_2016 = NULL,
        $progress_by_fall_2016 = NULL,
        $progress_by_mach_2017 = NULL,
        $progress_by_fall_2017 = NULL,
        $related_tasks_people = NULL,
        $related_tasks_description = NULL,
        $related_tasks_people2 = NULL,
        $related_tasks_description2 = NULL,
        $codes_tools = NULL,
        $code_tools_management = NULL,
        $point_of_contact = NULL,
        $terms = NULL,
        $ack1 = FALSE,
        $publications_resultant = NULL,
        $transitions = NULL,
        $presentations_resultant = NULL,
        $collaborators = NULL,
        $proposal_authors = array(),
        $extended_tech_images = array(),
        $pub_images = array(),
        $pub_uploads = array(),
        $budget_info_upload = array(),
        $budget_files = array(),
        $budget_uploads = array();

    private $add_proposal;

    /**
     * Set's the current user id
     * @param $id
     */
    public function set_current_user_id($id)
    {
        $this->current_user_id = $id;
    }

    /**
     * Create New Proposal
     */
    public function create()
    {
        $this->userdata = wp_get_current_user();
        $this->current_user_meta = get_user_meta($this->userdata->ID);

        $this->status = sanitize_text_field($_POST['status']);
        $this->title = sanitize_text_field($_POST['task_title']);
        $this->status = sanitize_text_field($_POST['status']);
        $this->task_number = sanitize_text_field($_POST['task_number']);
        $this->cmrg_group = sanitize_text_field($_POST['cmrg_group']);
        $this->period = sanitize_text_field($_POST['period_of_performance']);
        $this->continuing_task = sanitize_text_field($_POST['continuing_task']);
        $this->task_related_to = sanitize_text_field($_POST['task_related_to']);
        $this->science_objective = sanitize_post_field('science_objective', $_POST['science_objective'], $this->post_data['ID'], 'display');
        $this->science_objective_importance = sanitize_post_field('science_objective_importance', $_POST['science_objective_importance'], $this->post_data['ID'], 'display');
        $this->state_of_art_capabilities = sanitize_post_field('state_of_art_capabilities', $_POST['state_of_art_capabilities'], $this->post_data['ID'], 'display');
        $this->summary_of_research_strategy = sanitize_post_field('summary_of_research_strategy', $_POST['summary_of_research_strategy'], $this->post_data['ID'], 'display');
        $this->extended_tech_description = sanitize_text_field($_POST['extended_tech_description']);
        $this->progress_by_mach_2016 = sanitize_post_field('progress_by_mach_2016', $_POST['progress_by_mach_2016'], $this->post_data['ID'], 'display');
        $this->progress_by_fall_2016 = sanitize_post_field('progress_by_fall_2016', $_POST['progress_by_fall_2016'], $this->post_data['ID'], 'display');
        $this->progress_by_mach_2017 = sanitize_post_field('progress_by_mach_2017', $_POST['progress_by_mach_2017'], $this->post_data['ID'], 'display');
        $this->progress_by_fall_2017 = sanitize_post_field('_POST', $progress_by_fall_2017['progress_by_fall_2017'], $this->post_data['ID'], 'display');
        $this->related_tasks_people = sanitize_post_field('related_tasks_people', $_POST['related_tasks_people'], $this->post_data['ID'], 'display');
        $this->related_tasks_description = sanitize_post_field('related_tasks_description', $_POST['related_tasks_description'], $this->post_data['ID'], 'display');
        $this->related_tasks_people2 = sanitize_post_field('related_tasks_people2', $_POST['related_tasks_people2'], $this->post_data['ID'], 'display');
        $this->related_tasks_description2 = sanitize_post_field('related_tasks_description2', $_POST['related_tasks_description2'], $this->post_data['ID'], 'display');
        $this->codes_tools = sanitize_post_field('codes_tools', $_POST['codes_tools'], $this->post_data['ID'], 'display');
        $this->code_tools_management = sanitize_post_field('code_tools_management', $_POST['code_tools_management'], $this->post_data['ID'], 'display');
        $this->point_of_contact = sanitize_post_field('point_of_contact', $_POST['point_of_contact'], $this->post_data['ID'], 'display');

        $this->terms = sanitize_post_field('terms', $_POST['terms'], $this->post_data['ID'], 'display');


        $this->publications_resultant = sanitize_post_field('publications_resultant', $_POST['publications_resultant'], $this->post_data['ID'], 'display');
        $this->transitions = sanitize_post_field('transitions', $_POST['transitions'], $this->post_data['ID'], 'display');
        $this->presentations_resultant = sanitize_post_field('presentations_resultant', $_POST['presentations_resultant'], $this->post_data['ID'], 'display');
        $this->collaborators = format_collaborators($_POST['collaborator']);

        foreach ($this->collaborators as $collaborator) {
            if ($collaborator['collaborator_position'] == 'task_lead' || $collaborator['collaborator_position'] == 'task_co_lead') {
                array_push($this->proposal_authors, $collaborator['collaborator']);
            }
        }

        $this->post_data = array(
            'post_type' => 'proposal',
            'post_title' => $this->title,
            'post_author' => get_current_user_id(),
            'post_status' => $this->status,
        );

        $this->add_proposal = wp_insert_post($this->post_data);

        if ($this->add_proposal) {
            add_post_meta($this->add_proposal, 'task_title', $this->title, true);
            add_post_meta($this->add_proposal, 'task_number', $this->task_number, true);
            add_post_meta($this->add_proposal, 'continuing_task', $this->continuing_task, true);
            add_post_meta($this->add_proposal, 'task_related_to', $this->task_related_to, true);
            add_post_meta($this->add_proposal, 'period_of_performance', $this->period, true);
            add_post_meta($this->add_proposal, 'progress_by_mach_2016', $this->progress_by_mach_2016, true);
            add_post_meta($this->add_proposal, 'progress_by_fall_2016', $this->progress_by_fall_2016, true);
            add_post_meta($this->add_proposal, 'progress_by_mach_2017', $this->progress_by_mach_2017, true);
            add_post_meta($this->add_proposal, 'progress_by_fall_2017', $this->progress_by_fall_2017, true);
            add_post_meta($this->add_proposal, 'cmrg_group', $this->cmrg_group, true);
            add_post_meta($this->add_proposal, 'ctrg', $this->ctrg, true);
            add_post_meta($this->add_proposal, 'related_tasks_people2', $this->related_tasks_people2, true);
            add_post_meta($this->add_proposal, 'related_tasks_description2', $this->related_tasks_description2, true);
            add_post_meta($this->add_proposal, 'codes_tools', $this->codes_tools, true);
            add_post_meta($this->add_proposal, 'code_tools_management', $this->code_tools_management, true);
            add_post_meta($this->add_proposal, 'point_of_contact', $this->point_of_contact, true);
            add_post_meta($this->add_proposal, 'publications_resultant', $this->publications_resultant, true);

            add_post_meta($this->add_proposal, 'terms', $this->terms, true);



            add_post_meta($this->add_proposal, 'transitions', $this->transitions, true);
            add_post_meta($this->add_proposal, 'presentations_resultant', $this->presentations_resultant, true);
            add_post_meta($this->add_proposal, 'extended_tech_description', $this->extended_tech_description, true);
            add_post_meta($this->add_proposal, 'science_objective', $this->science_objective, true);
            add_post_meta($this->add_proposal, 'science_objective_importance', $this->science_objective_importance, true);
            add_post_meta($this->add_proposal, 'state_of_art_capabilities', $this->state_of_art_capabilities, true);
            add_post_meta($this->add_proposal, 'summary_of_research_strategy', $this->summary_of_research_strategy, true);
            add_post_meta($this->add_proposal, 'collaborators', $this->collaborators, true);
            add_post_meta($this->add_proposal, 'proposal_authors', $this->proposal_authors, true);

        $post_data = get_metadata('post', get_the_ID());
echo "<pre>";
print_r($post_data);
echo "</pre>";

            // if image submitted
            if (isset($_FILES['extended_tech_upload_basic']) && $_FILES['extended_tech_upload_basic']['size'] > 0) {

                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $post_data_upload = array();

                $mimes = array(
                    'jpg' => 'image/jpg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'png' => 'image/png',
                    'pdf' => 'application/pdf'
                );

                $overrides = array(
                    'test_form' => false,
                    'mimes' => $mimes
                );

                $attachment_id = media_handle_upload('extended_tech_upload_basic', $this->add_proposal, $post_data_upload, $overrides);

                if (is_wp_error($attachment_id)) {
                    echo 'There was an error uploading the image';
                }
            }

            // if budget form submitted
            if (isset($_FILES['budget_info_upload']) && $_FILES['budget_info_upload']['size'] > 0) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $files = $_FILES['budget_info_upload'];

                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                            'name' => $files['name'][$key],
                            'type' => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error' => $files['error'][$key],
                            'size' => $files['size'][$key]
                        );

                        $_FILES = array('budget_info_upload' => $file);

                        foreach ($_FILES as $file => $array) {
                            $newupload = my_handle_attachment($file, $this->add_proposal);
                            if (is_wp_error($newupload)) {
                                echo 'There was an error uploading the file';
                            } else {
                                array_push($this->budget_info_upload, $newupload[0]);
                            }
                        }
                    }
                }
                add_post_meta($this->add_proposal, 'budget_info_upload', $this->budget_info_upload, true);
            }

            // if publications files submitted
            if (isset($_FILES['publications_upload_basic']) && $_FILES['publications_upload_basic']['size'] > 0) {

                $files = $_FILES['publications_upload_basic'];

                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                            'name' => $files['name'][$key],
                            'type' => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error' => $files['error'][$key],
                            'size' => $files['size'][$key]
                        );

                        $_FILES = array('publications_upload_basic' => $file);

                        foreach ($_FILES as $file => $array) {
                            $newupload = my_handle_attachment($file, $this->add_proposal);

                            if (is_wp_error($newupload)) {
                                echo 'There was an error uploading the file';
                            } else {
                                array_push($this->pub_images, $newupload[0]);
                            }
                        }
                    }
                }
                add_post_meta($this->add_proposal, 'publications_upload_basic', $this->pub_images, true);
            }
        }
    }

    /**
     * Get Proposal
     * @return int
     */
    public function get_new_proposal_id()
    {
        return $this->add_proposal;
    }


    public function update()
    {
        $this->post_data = get_metadata('post', get_the_ID());
        $this->status = sanitize_text_field($_POST['status']);
        $this->title = sanitize_text_field($_POST['task_title']);

        $this->pub_images = array();
        $this->budget_files = array();

        $this->pub_uploads = maybe_unserialize($this->post_data['publications_upload_basic'][0]);
        $this->budget_uploads = maybe_unserialize($this->post_data['budget_info_upload'][0]);

        if (count($this->budget_uploads) > 0) {
            $this->budget_files = array_flatten($this->budget_uploads, array());
        }

        if (count($this->pub_uploads) > 0) {
            $this->pub_images = array_flatten($this->pub_uploads, array());
        }

        if (!$this->title) {
            $this->title = get_the_title();
        }

        $this->task_number = sanitize_text_field($_POST['task_number']);
        $this->cmrg_group = sanitize_text_field($_POST['cmrg_group']);
        $this->ctrg = sanitize_text_field($_POST['ctrg']);
        $this->period = sanitize_text_field($_POST['period_of_performance']);
        $this->continuing_task = sanitize_text_field($_POST['continuing_task']);
        $this->task_related_to = sanitize_text_field($_POST['task_related_to']);
        $this->science_objective = sanitize_post_field('science_objective', $_POST['science_objective'], get_the_ID(), 'display');
        $this->science_objective_importance = sanitize_post_field('science_objective_importance', $_POST['science_objective_importance'], get_the_ID(), 'display');
        $this->state_of_art_capabilities = sanitize_post_field('state_of_art_capabilities', $_POST['state_of_art_capabilities'], get_the_ID(), 'display');
        $this->summary_of_research_strategy = sanitize_post_field('summary_of_research_strategy', $_POST['summary_of_research_strategy'], get_the_ID(), 'display');
        $this->extended_tech_description = sanitize_post_field('extended_tech_description', $_POST['extended_tech_description'], get_the_ID(), 'display');
        $this->progress_by_mach_2016 = sanitize_post_field('progress_by_mach_2016', $_POST['progress_by_mach_2016'], get_the_ID(), 'display');
        $this->progress_by_fall_2016 = sanitize_post_field('progress_by_fall_2016', $_POST['progress_by_fall_2016'], get_the_ID(), 'display');
        $this->progress_by_mach_2017 = sanitize_post_field('progress_by_mach_2017', $_POST['progress_by_mach_2017'], get_the_ID(), 'display');
        $this->progress_by_fall_2017 = sanitize_post_field('progress_by_fall_2017', $_POST['progress_by_fall_2017'], get_the_ID(), 'display');
        $this->related_tasks_people = sanitize_post_field('related_tasks_people', $_POST['related_tasks_people'], get_the_ID(), 'display');
        $this->related_tasks_description = sanitize_post_field('related_tasks_description', $_POST['related_tasks_description'], get_the_ID(), 'display');
        $this->related_tasks_people2 = sanitize_post_field('related_tasks_people2', $_POST['related_tasks_people2'], get_the_ID(), 'display');
        $this->related_tasks_description2 = sanitize_post_field('related_tasks_description2', $_POST['related_tasks_description2'], get_the_ID(), 'display');
        $this->codes_tools = sanitize_post_field('codes_tools', $_POST['codes_tools'], get_the_ID(), 'display');
        $this->code_tools_management = sanitize_post_field('code_tools_management', $_POST['code_tools_management'], get_the_ID(), 'display');
        $this->point_of_contact = sanitize_post_field('point_of_contact', $_POST['point_of_contact'], get_the_ID(), 'display');
        $this->publications_resultant = sanitize_post_field('publications_resultant', $_POST['publications_resultant'], get_the_ID(), 'display');
        $this->transitions = sanitize_post_field('transitions', $_POST['transitions'], get_the_ID(), 'display');

        $this->terms = sanitize_post_field('terms', $_POST['terms'], get_the_ID(), 'display');

        $this->presentations_resultant = sanitize_post_field('presentations_resultant', $_POST['presentations_resultant'], get_the_ID(), 'display');

        $this->collaborators = array_push_assoc($this->collaborators, 'collaborator', $_POST['collaborator']);
        $this->collaborators = array_push_assoc($this->collaborators, 'collaborator_position', $_POST['collaborator_position']);
        $this->collaborators = format_collaborators($this->collaborators);

        foreach ($this->collaborators as $collaborator) {
            if ($collaborator['collaborator_position'] == 'task_lead' || $collaborator['collaborator_position'] == 'task_co_lead') {
                array_push($this->proposal_authors, $collaborator['collaborator']);
            }
        }

        $post = array(
            'ID' => get_the_ID(),
            'post_title' => $this->title,
            'post_author' => get_the_author_meta('ID'),
            'post_status' => $this->status
        );

        $update = wp_update_post($post, FALSE);

        if ($update) {

            if ($update) {
                if ($this->title) {
                    update_post_meta(get_the_ID(), 'task_title', $this->title);
                }
                if ($this->task_number) {
                    update_post_meta(get_the_ID(), 'task_number', $this->task_number);
                }
                if ($this->continuing_task) {
                    update_post_meta(get_the_ID(), 'continuing_task', $this->continuing_task);
                }
                if ($this->task_related_to) {
                    update_post_meta(get_the_ID(), 'task_related_to', $this->task_related_to);
                } else {
                    delete_post_meta(get_the_ID(), 'task_related_to');
                }
                if ($this->progress_by_mach_2016) {
                    update_post_meta(get_the_ID(), 'progress_by_mach_2016', $this->progress_by_mach_2016);
                }
                if ($this->progress_by_fall_2016) {
                    update_post_meta(get_the_ID(), 'progress_by_fall_2016', $this->progress_by_fall_2016);
                }
                if ($this->progress_by_mach_2017) {
                    update_post_meta(get_the_ID(), 'progress_by_mach_2017', $this->progress_by_mach_2017);
                }
                if ($this->progress_by_fall_2017) {
                    update_post_meta(get_the_ID(), 'progress_by_fall_2017', $this->progress_by_fall_2017);
                }
                if ($this->cmrg_group) {
                    update_post_meta(get_the_ID(), 'cmrg_group', $this->cmrg_group);
                }
                if ($this->ctrg) {
                    update_post_meta(get_the_ID(), 'ctrg', $this->ctrg);
                }
                if ($this->period) {
                    update_post_meta(get_the_ID(), 'period_of_performance', $this->period);
                }
                if ($this->related_tasks_people) {
                    update_post_meta(get_the_ID(), 'related_tasks_people', $this->related_tasks_people);
                }
                if ($this->related_tasks_description) {
                    update_post_meta(get_the_ID(), 'related_tasks_description', $this->related_tasks_description);
                }
                if ($this->related_tasks_people2) {
                    update_post_meta(get_the_ID(), 'related_tasks_people2', $this->related_tasks_people2);
                }
                if ($this->related_tasks_description2) {
                    update_post_meta(get_the_ID(), 'related_tasks_description2', $this->related_tasks_description2);
                }
                if ($this->codes_tools) {
                    update_post_meta(get_the_ID(), 'codes_tools', $this->codes_tools);
                }
                if ($this->code_tools_management) {
                    update_post_meta(get_the_ID(), 'code_tools_management', $this->code_tools_management);
                }
                if ($this->point_of_contact) {
                    update_post_meta(get_the_ID(), 'point_of_contact', $this->point_of_contact);
                }
                if ($this->publications_resultant) {
                    update_post_meta(get_the_ID(), 'publications_resultant', $this->publications_resultant);
                }
                if ($this->terms) {
                    update_post_meta(get_the_ID(), 'terms', $this->terms);
                };
                if ($this->transitions) {
                    update_post_meta(get_the_ID(), 'transitions', $this->transitions);
                };
                if ($this->presentations_resultant) {
                    update_post_meta(get_the_ID(), 'presentations_resultant', $this->presentations_resultant);
                }
                if ($this->extended_tech_description) {
                    update_post_meta(get_the_ID(), 'extended_tech_description', $this->extended_tech_description);
                };
                if ($this->science_objective) {
                    update_post_meta(get_the_ID(), 'science_objective', $this->science_objective);
                }
                if ($this->science_objective_importance) {
                    update_post_meta(get_the_ID(), 'science_objective_importance', $this->science_objective_importance);
                }
                if ($this->state_of_art_capabilities) {
                    update_post_meta(get_the_ID(), 'state_of_art_capabilities', $this->state_of_art_capabilities);
                };
                if ($this->summary_of_research_strategy) {
                    update_post_meta(get_the_ID(), 'summary_of_research_strategy', $this->summary_of_research_strategy);
                }
                if ($this->collaborators) {
                    update_post_meta(get_the_ID(), 'collaborators', $this->collaborators);
                }
                if (!isset($_POST['extended_tech_upload_uploaded'])) {
                    delete_post_meta(get_the_ID(), 'extended_tech_upload_basic', $this->post_data['extended_tech_upload_basic']);
                }
                if ($this->proposal_authors) {
                    update_post_meta(get_the_ID(), 'proposal_authors', $this->proposal_authors);
                }

                // if image submitted
                if (isset($_FILES['extended_tech_upload_basic']) && $_FILES['extended_tech_upload_basic']['size'] > 0) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');

                    $post_data_upload = array();

                    $mimes = array(
                        'jpg' => 'image/jpg',
                        'jpeg' => 'image/jpeg',
                        'gif' => 'image/gif',
                        'png' => 'image/png',
                        'pdf' => 'application/pdf'
                    );

                    $overrides = array(
                        'test_form' => false,
                        'mimes' => $mimes
                    );

                    $attachment_id = media_handle_upload('extended_tech_upload_basic', get_the_ID(), $post_data_upload, $overrides);

                    if (is_wp_error($attachment_id)) {
                        echo 'There was an error uploading the image';
                    } else {
                        update_post_meta(get_the_ID(), 'extended_tech_upload_basic', $attachment_id);
                    }
                }

                if (!isset($_POST['publications_upload_basic_uploaded'])) {
                    delete_post_meta(get_the_ID(), 'publications_upload_basic');
                }

                if ($_POST['publications_upload_basic_uploaded']) {
                    $current_values = array_flatten($this->pub_uploads, array());
                    $post_pub_uploads = $_POST['publications_upload_basic_uploaded'];

                    $removedItems = array_diff($this->pub_images, $post_pub_uploads);

                    if (count($removedItems) > 0) {
                        foreach ($removedItems as $item) {
                            $this->pub_images = array_delete($item, $this->pub_images);
                        }
                        update_post_meta(get_the_ID(), 'publications_upload_basic', $this->pub_images);
                    }
                }


                if ((isset($_FILES['publications_upload_basic']) && $_FILES['publications_upload_basic']['size'][0] > 0)) {

                    $files = $_FILES['publications_upload_basic'];

                    foreach ($files['name'] as $key => $value) {
                        if ($files['name'][$key]) {
                            $file = array(
                                'name' => $files['name'][$key],
                                'type' => $files['type'][$key],
                                'tmp_name' => $files['tmp_name'][$key],
                                'error' => $files['error'][$key],
                                'size' => $files['size'][$key]
                            );

                            $_FILES = array('publications_upload_basic' => $file);

                            foreach ($_FILES as $file => $array) {
                                $newupload = my_handle_attachment($file, get_the_ID());
                                if (is_wp_error($newupload)) {
                                    echo 'There was an error uploading the file';
                                } else {
                                    array_push($this->pub_images, $newupload[0]);
                                }
                            }
                        }
                    }
                    update_post_meta(get_the_ID(), 'publications_upload_basic', $this->pub_images);
                }

                if (!isset($_POST['budget_info_uploaded'])) {
                    $this->budget_files = array();
                    delete_post_meta(get_the_ID(), 'budget_info_upload');
                }

                if ($_POST['budget_info_uploaded']) {
                    $current_values = array_flatten($this->budget_files, array());
                    $post_budget_uploads = $_POST['budget_info_uploaded'];

                    $removedItems = array_diff($this->budget_files, $post_budget_uploads);

                    if (count($removedItems) > 0) {
                        foreach ($removedItems as $item) {
                            $this->budget_files = array_delete($item, $this->budget_files);
                        }
                        update_post_meta(get_the_ID(), 'budget_info_upload', $this->budget_files);
                    }
                }

                //print_r($this->budget_files);
                // if budget form submitted
                if (isset($_FILES['budget_info_upload']) && $_FILES['budget_info_upload']['size'] > 0) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');

                    $files = $_FILES['budget_info_upload'];

                    foreach ($files['name'] as $key => $value) {
                        if ($files['name'][$key]) {
                            $file = array(
                                'name' => $files['name'][$key],
                                'type' => $files['type'][$key],
                                'tmp_name' => $files['tmp_name'][$key],
                                'error' => $files['error'][$key],
                                'size' => $files['size'][$key]
                            );
                            $_FILES = array('budget_info_upload' => $file);

                            foreach ($_FILES as $file => $array) {
                                $newupload = my_handle_attachment($file, get_the_ID());
                                if (is_wp_error($newupload)) {
                                    echo 'There was an error uploading the file';
                                } else {
                                    array_push($this->budget_files, $newupload[0]);
                                }
                            }
                        }
                    }
                    update_post_meta(get_the_ID(), 'budget_info_upload', $this->budget_files);
                }
            }
        }
    }

    /**
     * Get Proposal Authors
     * @param $post_id
     * @return mixed
     */
    public
    function get_proposal_authors($post_id)
    {
        return get_post_meta($post_id, 'proposal_authors', FALSE);
    }

    /**
     * Checks to see if user is author of specified proposal
     * @param $post_id
     * @param $user_id
     * @return bool
     */
    public
    function is_author($user_id, $post_id)
    {
        // get original post
        $original_post_author = get_post_field('post_author', $post_id);
        // get proposal author
        $authors = $this->get_proposal_authors($post_id);
        $authors = $authors[0];
        // push original proposal
        array_push($authors, $original_post_author);
        // removes duplicates
        $authors = array_unique($authors);

        if (in_array($user_id, $authors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cleans Position Output
     * @param $position
     * @return string
     */
    public function collaborator_position_output($position)
    {
        $output;
        switch ($position) {
            case 'task_lead':
                $output = 'Task Lead';
                break;
            case 'task_co_lead':
                $output = 'Task Co-Lead';
                break;
            case 'arl_collaborator':
                $output = 'ARL Collaborator';
                break;
            case 'postdoc':
                $output = 'Postdoc';
                break;
            case 'graduate_student':
                $output = 'Graduate Student';
                break;
            default:
                $output = 'Other';

        }
        return $output;
    }

    /**
     * @param $proposal
     * @param $user_id
     */
    public function lock_proposal($proposal, $user_id)
    {
        global $user_level;
        // If user is authorized to edit continue
        if ($this->is_author($user_id, $proposal) || 'admin' == $user_level || $user_level == 'cmrg_lead') {

            update_post_meta($proposal, '_edit_last', $user_id);
            return wp_set_post_lock($proposal);
        }
    }

    /**
     * Checks to see if proposal is currently locked.
     * @param $proposal (POST_ID)
     */
    public function is_currently_locked($proposal)
    {
        return wp_check_post_lock($proposal);
    }

    public function locked_by($proposal)
    {
        $lock = get_post_meta($proposal, '_edit_lock', true);
        $lock = explode(':', $lock);
        return $lock[1];
    }

    /**
     * @param $proposal
     * @return bool|int
     */
    public function unlock_proposal($proposal)
    {
        delete_post_meta($proposal, '_edit_last');
        delete_post_meta($proposal, '_edit_lock');
    }

}
