<?php
global $user_level;

if ($user_level != 'no_access' || $user_level != 'public'):

    $proposal = new Proposal();
    $is_author = $proposal->is_author(get_current_user_id(), get_the_ID());



    // check if locked
    $is_locked = $proposal->is_currently_locked(get_the_ID());

    if (isset($_GET['locked']) && $_GET['locked'] == 'true') {
        if ($is_locked == false) {
            $lock = $proposal->lock_proposal(get_the_ID(), get_current_user_id());
        }

    }


    // If current user is locking proposal, tell them.
    if ($is_locked == false && ($lock[1] == get_current_user_id())) {
        echo '<p class="alert-success"> This proposal is currently locked by you. To unlock this proposal, please save progress and <a href="?locked=false">click here</a>. </p>';
    } else {
        //redirect if you should have access
        echo('<script>window.location = "' . get_permalink(get_the_ID()) . '"</script>');
    }
    ?>

    <?php if ($is_author || $user_level == 'admin' || $user_level == 'cmrg_lead'): ?>


    <main id="main" class="site-main" role="main">

        <h1>Editing: <?php the_title(); ?> </h1>

        <?php while (have_posts()) : the_post(); ?>
            <?php
            $errors = array();
            $msg = array();
            $error = 0;
            $media = get_attached_media('image');
            $status = get_post_status();
            $pub_images = array();
            $post_data = get_metadata('post', get_the_ID());
            $pub_uploads = maybe_unserialize($post_data['publications_upload_basic'][0]);
            $budget_uploads = maybe_unserialize($post_data['budget_info_upload'][0]);

            if( !is_array($budget_uploads)) {
                $budget_uploads = array($budget_uploads);
            }

            if (count($pub_uploads) > 0) {
                $pub_images = array_flatten($pub_uploads, array());
            }
            if( count($budget_uploads) > 0 ) {
                $budget_files = array_flatten($budget_uploads, array() );
            }
            if ('POST' == $_SERVER['REQUEST_METHOD']):

                if (isset($_POST['update_proposal_nonce']) && wp_verify_nonce($_POST['update_proposal_nonce'], 'update_proposal')) :

                    $proposal->update();

                    if (('in_review' == $_POST['status']) || ('submitted' == $_POST['status'])) { // check if the status is set to in_review or submitted if so, send an email to let the CMRGs know. The array for the recipients is returned by function mede_construct_cmrg_lead_email_array().

                        // Build the email out
                        $subject = 'A proposal in your group has been marked as submitted, or in review.';
                        $message = "View the proposal here: " . get_permalink(get_the_ID());

                        // you've got mail!
                        wp_mail(mede_construct_cmrg_lead_email_array(get_the_author_meta('ID')), $subject, $message);


                    } // end if ( ( 'in_review' == $_POST['status'] ) || ( 'submitted' == $_POST['status'] ) )


                    if ('investigator' == $user_level || 'cmrg_lead' == $user_level) {
                        //echo('<script>window.location = "'. home_url() . '?added=true&status=' . $status . '"</script>');
                         echo('<script>window.location = "' . get_permalink(  get_the_ID() ) . '?locked=false"</script>');
                    } else {
                        // echo('<script>window.location = "' . get_permalink(  get_the_ID() ) . '?updated=true"</script>');
                    }

                endif;
            endif; ?>

            <?php
            $post_data = get_metadata('post', get_the_ID());
            $pub_uploads = maybe_unserialize($post_data['publications_upload_basic'][0]);
            $period = $post_data['period_of_performance'][0];
            $period_with_to = str_replace('_to_', ' - ', $period);
            $period_final = str_replace('_', '/', $period_with_to);

            $collaborators = maybe_unserialize($post_data['collaborators'][0]);

            ?>

            <div class="proposal container page-template-add-proposal ">
                <div class="proposal__wrapper">
                    <form id="addProposal" method="post" action="" enctype="multipart/form-data">

                        <fieldset id="general">
                            <legend>General</legend>

                            <!-- TASK TITLE -->
                            <div>
                                <label for="task_title"><?php _e('Task Title', 'mede') ?></label>
                                <input type="text" name="task_title" id="task_title" value="<?php echo $post_data['task_title'][0]; ?>" <?php if ('admin' == $user_level) {
                                    echo 'disabled="disabled"';
                                } ?>/>
                            </div>

                            <!-- TASK NUMBER -->
                            <?php if ('cmrg_lead' == $user_level) { ?>
                                <div>
                                    <label for="task_number">Task Number</label>
                                    <input type="text" name="task_number" id="task_number" value="<?php echo $post_data['task_number'][0]; ?>" <?php if ('admin' == $user_level) {
                                        echo 'disabled="disabled"';
                                    } ?> />
                                </div>
                            <?php }; ?>

                            <!-- CMRG GROUP -->
                            <div>
                                <label for="cmrg_group">Collaborative Materials Research Group (CMRG)</label>
                                <select name="cmrg_group" id="cmrg_group" <?php if ('admin' == $user_level) {
                                    echo 'disabled="disabled"';
                                } ?>>
                                    
                                    <option value="ceramics" <?php if ($post_data['cmrg_group'][0] == 'ceramics') {
                                        echo 'selected="selected"';
                                    } ?>>Ceramics
                                    </option>
                                    <option value="composites" <?php if ($post_data['cmrg_group'][0] == 'composites') {
                                        echo 'selected="selected"';
                                    } ?>>Composites
                                    </option>
                                    <option value="polymers" <?php if ($post_data['cmrg_group'][0] == 'polymers') {
                                        echo 'selected="selected"';
                                    } ?>>Polymers
                                    </option>
                                    <option value="other" <?php if ($post_data['cmrg_group'][0] == 'other') {
                                        echo 'selected="selected"';
                                    } ?>>Other/Integrative
                                    </option>
                                </select>
                            </div>

                            

                            <!-- PERIOD OF PERFORMANCE -->
                            <div>
                                <label for="period_of_performance">Period Performance</label>
                                <select name="period_of_performance" id="period_of_performance" <?php if ('admin' == $user_level) {
                                    echo 'disabled="disabled"';
                                } ?>>
                                    <option value=""></option>
                                    <option value="01_01_2018_to_12_31_2019" <?php if ($period == '01_01_2018_to_12_31_2019') {
                                        echo 'selected="selected"';
                                    } ?>>1/1/18 – 12/31/19
                                    </option>
                                    <option value="other" <?php if ($period == 'other') {
                                        echo 'selected="selected"';
                                    } ?>>Other
                                    </option>
                                </select>
                            </div>

                            <!-- CONTINUING TASK -->
                            <div>
                                <label for="continuing_task">Continuing Task</label>
                                <select name="continuing_task" id="continuing_task" <?php if ('admin' == $user_level) {
                                    echo 'disabled="disabled"';
                                } ?>>
                                    <option value="yes" <?php if ($post_data['continuing_task'][0] == 'yes') {
                                        echo 'selected="selected"';
                                    } ?>>Yes
                                    </option>
                                    <option value="no" <?php if ($post_data['continuing_task'][0] == 'no') {
                                        echo 'selected="selected"';
                                    } ?>>No
                                    </option>
                                    <option value="new_related" <?php if ($post_data['continuing_task'][0] == 'new_related') {
                                        echo 'selected="selected"';
                                    } ?>>New Task Related to Existing Task
                                    </option>
                                </select>

                                <div id="task_related_to" class="task-related hide">
                                    <label for="task_related_to">Existing task this proposal is related to:</label>
                                    <input type="text" name="task_related_to" id="task_related_to" value="<?php echo $post_data['task_related_to'][0]; ?>" <?php if ('admin' == $user_level) {
                                        echo 'disabled="disabled"';
                                    } ?> />
                                </div>
                            </div>
                        </fieldset>

                        <!-- INVESTIGATORS -->
                        <fieldset>
                            <legend>Investigator(s) and Collaborator(s)</legend>
                            <div class="repeat">
                                <table class="wrapper" width="100%">
                                    <thead>
                                    <tr>
                                        <td width="10%" colspan="4"><span class="add pointer">Add</span></td>
                                        Please add co-PIs here. All CMEDE researchers directly affiliated with JHU are listed under "Co-PI".
                                      </tr>
                                      <br>
                                    </thead>
                                    <tbody class="container">
                                    <tr class="template row">
                                        <td><span class="pointer move"><i class="icon-cursor-move-two"></i> Move</span>
                                        </td>
                                        <td>
                                            <dl>
                                                <dt>Co-PI</dt>
                                                <dd>
                                                    <select name="collaborator[][collaborator]" id="collaborator_id">
                                                        <?php mede_display_collaborators_dropdown(); ?>
                                                    </select>
                                                </dd>

                                                <dt>Co-PI Role</dt>
                                                <dd>
                                                    <select name="collaborator[][collaborator_position]" id="collaborator_position">
                                                        <option value="task_lead">Investigator</option>
                                                        <option value="arl_collaborator">ARL Collaborator</option>
                                                        <option value="postdoc">Postdoc</option>
                                                        <option value="graduate Student">Graduate Student</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </dd>
                                                <dt>Phone</dt>
                                                <dd><input type="text" name="collaborator[][phone]"></dd>
                                            </dl>
                                        </td>
                                        <td><span class="pointer remove"><i class="icon-remove"></i> Remove</span></td>
                                    </tr>

                                    <?php
                                    $i = 0;

                                    foreach ($collaborators as $collaborator):

                                        ?>
                                        <tr class="row">
                                            <td>
                                                <span class="pointer move"><i class="icon-cursor-move-two"></i> Move</span>
                                            </td>
                                            <td>
                                                <dl>

                                                    <dt>Co-PI</dt>
                                                    <dd>                                                    
                                                    
                                                        <select name="collaborator[][collaborator]" id="collaborator_id" value="<?php echo $collaborator['collaborator']; ?>">
                                                            <?php mede_display_collaborators_dropdown($collaborator['collaborator']); ?>
                                                        </select>
                                                    
                                                  </dd>

                                                    <dt>Co-PI Role</dt>
                                                    <dd>
                                                        <select name="collaborator[][collaborator_position]" id="collaborator_position">
                                                            <option value="task_lead" <?php if ($collaborator['collaborator_position'] == 'task_lead') {
                                                                echo 'selected="selected"';
                                                            } ?> >Investigator
                                                            </option>                                                         
                                                            <option value="arl_collaborator" <?php if ($collaborator['collaborator_position'] == 'arl_collaborator') {
                                                                echo 'selected="selected"';
                                                            } ?> >ARL Collaborator
                                                            </option>
                                                            <option value="postdoc" <?php if ($collaborator['collaborator_position'] == 'postdoc') {
                                                                echo 'selected="selected"';
                                                            } ?>>Postdoc
                                                            </option>
                                                            <option value="graduate_student" <?php if ($collaborator['collaborator_position'] == 'graduate_student') {
                                                                echo 'selected="selected"';
                                                            } ?>>Graduate Student
                                                            </option>
                                                            <option value="other" <?php if ($collaborator['collaborator_position'] == 'other') {
                                                                echo 'selected="selected"';
                                                            } ?>>Other
                                                            </option>
                                                        </select>
                                                    </dd>
                                                    <dt>Phone</dt>
                                                    <dd>
                                                        <input type="text" name="collaborator[][phone]" value="<?php echo $collaborator['phone']; ?>"/>
                                                    </dd>
                                                </dl>
                                                <dl class="hide collaborator_position_other">
                                                    <dt>First Name</dt>
                                                    <dd>
                                                        <input type="text" name="collaborator[][first_name]" value="<?php echo $collaborator['first_name']; ?>">
                                                    </dd>
                                                    <dt>Last Name</dt>
                                                    <dd>
                                                        <input type="text" name="collaborator[][last_name]" value="<?php echo $collaborator['last_name']; ?>">
                                                    </dd>
                                                    <dt>Email</dt>
                                                    <dd>
                                                        <input type="text" name="collaborator[][email]" value="<?php echo $collaborator['email']; ?>">
                                                    </dd>
                                                    <dt>Affiliation</dt>
                                                    <dd>
                                                        <select name="collaborator[][affiliation]" id="collaborator_other_affiliation" class="collaborator_affiliation">
                                                            <option></option>
                                                            <?php mede_display_affiliations_as_choices_in_add_or_edit($collaborator['affiliation']); ?>
                                                        </select>
                                                    </dd>
                                                    <div class="hide other_affiliation">
                                                        <dt>Other Affiliation</dt>
                                                        <dd>
                                                            <input type="text" name="collaborator[][affiliation_other]" value="<?php echo $collaborator['affiliation_other'] ?>">
                                                        </dd>
                                                    </div>
                                                </dl>
                                            </td>
                                            <td><span class="pointer remove"><i class="icon-remove"></i> Remove</span>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                      
                      
            <!-- Collaborators -->
                <fieldset>
                    <legend>Collaborators (Outside JHU)</legend>

                    <div class="repeat">
                        <table class="wrapper" width="100%">
                            <thead>
                            <tr>
                                <td width="10%" colspan="4"><span class="add pointer">Add</span>   Please click add to manually enter co-PI's that you were unable to find in the section above.</td> 
                              Please ONLY manually add collaborators if you could not find them from the drop-down list in the section above.
                              <br>
                              </tr>
                            </thead>
                          <br>
                            <tbody class="container">
                            
<tr class="template row">
                                <td><span class="pointer move"><i class="icon-cursor-move-two"></i> Move</span></td>
                                <td>
<!--                                     <dl class="hide collaborator_position_other"> -->

                                        <dt>First Name</dt>
                                        <dd><input type="text" name="collaborator[][first_name]"></dd>
                                        <dt>Last Name</dt>
                                        <dd><input type="text" name="collaborator[][last_name]"></dd>

                                        <dt>Email</dt>
                                        <dd><input type="text" name="collaborator[][email]"></dd>
                                        <dt>Affiliation</dt>
                                        <dd>
                                            <select name="collaborator[][affiliation]" id="collaborator_other_affiliation" class="collaborator_affiliation">
                                                <option></option>
                                                <?php mede_display_affiliations_as_choices_in_add_or_edit(); ?>
                                            </select>
                                        </dd>
                                        <div class="hide other_affiliation">
                                            <dt>Other Affiliation</dt>
                                            <dd><input type="text" name="collaborator[][affiliation_other]"></dd>
                                        </div>
<!--                                     </dl> -->
                                </td>
                                <td><span class="pointer remove"><i class="icon-remove"></i> Remove</span></td>
                            </tr>

                            

                            </tbody>
                        </table>
                    </div>
    
                </fieldset>

                      
            <!-- Research Summary -->

                        <fieldset>
                            <legend>Research Summary</legend>

                            <label for="science_objective">Problem Statement, Scientific Objectives, and Scientific Needs <br/><span>State the key aspect(s) and scientific objective(s) of the task.</span></label>
                            <textarea name="science_objective" id="science_objective" data-maxlength="750" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['science_objective'][0]); ?></textarea>

                            <label for="science_objective_importance">Importance of These Science Objectives<br/><span>Why do we need to achieve these objectives, relative to the problem statement?</span></label>
                            <textarea name="science_objective_importance" id="science_objective_importance" data-maxlength="750" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['science_objective_importance'][0]); ?></textarea>

                            <label for="state_of_art_capabilities">State-of-the-Art/Capabilities In This
                                Subject Area<br/><span>What has already been done here or elsewhere?</span></label>
                            <textarea name="state_of_art_capabilities" id="state_of_art_capabilities" data-maxlength="750" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['state_of_art_capabilities'][0]); ?></textarea>

                            <label for="summary_of_research_strategy">Summary of Research Strategy<br/><span>How will we address our long-term science objectives?</span></label>
                            <textarea name="summary_of_research_strategy" id="summary_of_research_strategy" data-maxlength="750" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['summary_of_research_strategy'][0]); ?></textarea>
                        </fieldset>

                        <fieldset>
                            <legend>Extended Technical Description</legend>
                            <label for="extended_tech_description">This description should include how this task ties into your CMRG Problem Statement, Scientific Objectives and Scientific Needs.</label>
                            <textarea name="extended_tech_description" id="extended_tech_description" data-maxlength="4000" cols="30" rows="20" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['extended_tech_description'][0]); ?></textarea>
                            <label for="extended_tech_upload_basic">Include one image; upload image file (file format:
                                *jpeg, .jpg, .png, .pdf)</label>
                            <input type="file" name="extended_tech_upload_basic" class="extended_tech_upload_basic" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>/>

                            <?php
                            if ($post_data['extended_tech_upload_basic'][0]) {
                                echo '<h3>File(s) already attached to this proposal:</h3>';
                                echo '<ul class="download-listing">';
                                $pub_info = get_post($post_data['extended_tech_upload_basic'][0]);
                                echo '<li><a target="_blank" href="' . $pub_info->guid . '">' . $pub_info->post_name . ' </a> <span class="delete">X</span>';
                                echo '<input type="hidden" name="extended_tech_upload_uploaded" value="' . $pub_info->ID . '"/>';
                                echo '</li>';
                                echo '</ul>';
                            }
                            ?>

                        </fieldset>

                        <fieldset>
                            <! -- SCIENCE Goals -->
                            <legend>Science Goals/Expected Progress</legend>

                            <label for="progress_by_mach_2016">What do you expect to have done by the 2018 Fall Meeting?<br/><span>Describe in terms of milestones and decision points.</span></label>
                            <textarea name="progress_by_mach_2016" id="progress_by_mach_2016" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['progress_by_mach_2016'][0]); ?></textarea>

                            <label for="progress_by_fall_2016">What do you expect to have done by the 2019 MEDE Fall Meeting?<br/><span>Describe in terms of milestones and decision points.</span></label>
                            <textarea name="progress_by_fall_2016" id="progress_by_fall_2016" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['progress_by_fall_2016'][0]); ?></textarea>

                        </fieldset>


                        <fieldset>
                            <legend>Related Tasks and Intertask Collaborations</legend>

                            <!-- RELATED TASKS: PEOPLE -->
                            <label for="related_tasks_people">Person(s) with whom you are collaborating within your group and the CMRG:</label>
                            <textarea name="related_tasks_people" id="related_tasks_people" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['related_tasks_people'][0]); ?></textarea>

                            <!-- RELATED TASKS: DESCRIPTION -->
                            <label for="related_tasks_description">Describe Collaborative Exchanges:</label>
                            <textarea name="related_tasks_description" id="related_tasks_description" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['related_tasks_description'][0]); ?></textarea>

                            <hr/>

                            <!-- RELATED TASKS: PEOPLE -->
                            <label for="related_tasks_people2">Person(s) with whom you are collaborating within your group and the CMRG::</label>
                            <textarea name="related_tasks_people2" id="related_tasks_people2" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['related_tasks_people2'][0]); ?></textarea>

                            <!-- RELATED TASKS: DESCRIPTION -->
                            <label for="related_tasks_description2">Describe Collaborative Exchanges:</label>
                            <textarea name="related_tasks_description2" id="related_tasks_description2" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['related_tasks_description2'][0]); ?></textarea>

                        </fieldset>

                        <fieldset>
                            <legend>Codes, Tools and Data Management</legend>
                            <!-- CODES -->
                            <label for="codes_tools">Briefly describe the (i) codes (if any), (ii) tools (if any), and
                                (iii) data (type and magnitude, e.g. images, text files, corresponding sizes) that will
                                be created during this task.</label>
                            <textarea name="codes_tools" id="codes_tools" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['codes_tools'][0]); ?></textarea>

                            <!-- MANAGEMENT -->
                            <label for="code_tools_management">Briefly describe where these (i) codes (if any) and (ii)
                                data are stored and backed-up. Describe your approach to sharing your codes, data and
                                knowledge with the Consortium and ARL.</label>
                            <textarea name="code_tools_management" id="code_tools_management" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['code_tools_management'][0]); ?></textarea>

                          <!-- EULA #1 -->
                                        <p style="font-style: italic;"> It is expected that all relevant data and publications generated during MEDE-funded research will be uploaded to the MEDE Document and Data Sharing Infrastructure (DDSI) servers beginning in 2018.  In particular, the primary data associated with any submitted publication that acknowledges MEDE support should be uploaded to the DDSI servers and thus made available to all MEDE collaborators. Specific instructions for accessing and loading data to the DDSI servers will be provided at the MEDE Fall Meeting.  </p>
                                        <p style="font-weight: bold">MEDE tasks that do not adhere to this expectation could have their funding impacted.</p>
                                        <p style="font-weight: bold;">The PI hereby acknowledges this expectation: Select YES or NO:
                                            
                                            
                                          <?php
                                          if ($post_data['terms'][0] == 'yes') {
                                            echo '<label style="display: inline;"><input type="radio" name="terms" value="yes" checked>Yes</label>';
                                            echo '<label style="display: inline;"><input type="radio" name="terms" value="no">No</label>';
                                          } elseif ($post_data['terms'][0] == 'no') {
                                            echo '<label style="display: inline;"><input type="radio" name="terms" value="yes">Yes</label>';
                                            echo '<label style="display: inline;"><input type="radio" name="terms" value="no" checked>No</label>';
                                          } else {
                                            echo'<label style="display: inline;"><input type="radio" name="terms" value="yes">Yes</label>';
                                            echo '<label style="display: inline;"><input type="radio" name="terms" value="no">No</label>';
                                          }
                                          
                                          ?>
                                          
                                             </p>
                                        
                                      <br>                              
                                        <p style="font-style: italic;">NOTE: PIs that have demonstrably taken advantage of data sharing through this framework to increase collaboration and productivity (as judged by the MEDE RPM and CAM) may be able to access additional research funding in the second year of the BPP (contingent on availability and approval of funds).</p>
                                          <p style="font-weight: bold;">Are you interested in demonstrating the use of data sharing for collaboration under this opportunity?
                                            
                                            <?php
                                                if ($post_data['terms2'][0] == 'yes') {
                                                echo '<label style="display: inline;"><input type="radio" name="terms2" value="yes" checked>Yes</label>';
                                                echo '<label style="display: inline;"><input type="radio" name="terms2" value="no">No</label>';
                                              } elseif ($post_data['terms2'][0] == 'no') {
                                                echo '<label style="display: inline;"><input type="radio" name="terms2" value="yes">Yes</label>';
                                                echo '<label style="display: inline;"><input type="radio" name="terms2" value="no" checked>No</label>';
                                              } else {
                                                echo'<label style="display: inline;"><input type="radio" name="terms2" value="yes">Yes</label>';
                                                echo '<label style="display: inline;"><input type="radio" name="terms2" value="no">No</label>';
                                             }
                                            ?>
                                        </p>
                      </fieldset>               
                                      <!-- EULA #2 -->
                          <fieldset>
                          
                            <!-- POINT OF CONTACT -->
                            <label for="point_of_contact">Who is your point of contact for responding to requests
                                related to your codes, tools and data? (Name and email.)</label>
                            <textarea name="point_of_contact" id="point_of_contact" cols="30" rows="10" <?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>><?php echo esc_html($post_data['point_of_contact'][0]); ?></textarea>

                        </fieldset>

                        <fieldset>
                            <legend>Publications, Presentations, and Patents</legend>

                          <!-- PRESENTATIONS -->
                            <label for="publications_resultant">Within the context and timeframe for BPP FY16-17 (CY16-17), list the relevant publications, presentations, and patents that have resulted from your MEDE efforts.</label>
                            <textarea name="publications_resultant" id="publications_resultant" cols="30" rows="10"<?php if ('admin' == $user_level) {
                                echo 'disabled="disabled"';
                            } ?>></textarea>

                        </fieldset>


                        <fieldset>
                            <legend>Budget Information</legend>

                            <!-- DOWNLOAD BUDGET FORM -->
                            <p><strong>Instructions:</strong>
                                <a href="<?php echo get_stylesheet_directory_uri() . '/assets/MEDE BPP FY18-19 budget justification v2.docx'; ?>">Review
                                    the FY16-17 (CY16-17) budget guidelines</a>, then
                                <a href="<?php echo get_stylesheet_directory_uri() . '/assets/MEDE BPP FY18-19 budget worksheet v2.xlsx'; ?>">download
                                    this budget form</a>, save it as a .pdf or .xlsx, and upload it using the button
                                below.</p>

                            <!-- UPLOAD THE FILE -->
                            <label for="budget_info_upload">Include file(s); upload only a .pdf or .xlsx format
                                file</label>
                            <input type="file" accept=".xls, .xlsx, .pdf" name="budget_info_upload[]" class='budget_info_upload' multiple="multiple"/>
                            <ol class="bud-pending-uploads"></ol>

                            <?php
                            if (is_array($budget_uploads)) {
                                echo '<h3>File(s) already attached to this proposal:</h3>';
                                $i = 0;
                                echo '<ul class="download-listing">';
                                for ($i = 0; $i < count($budget_uploads); $i++) {
                                    $budget_upload = $budget_uploads[$i];
                                    if ($budget_upload) {
                                        $budget_info = get_post($budget_upload);
                                        echo '<li><a target="_blank" href="' . $budget_info->guid . '">' . $budget_info->post_name . '</a><span class="delete">X</span>';
                                        echo '<input type="hidden" name="budget_info_uploaded[' . $i . ']" value="' . $budget_info->ID . '"/>';
                                        echo '</li>';
                                    }
                                }
                                echo '</ul>';
                            } else {
                                echo '<ol class="download-listing">';
                                if ($budget_uploads) {
                                    $budget_info = get_post($budget_uploads);
                                    echo '<li><a target="_blank" href="' . $budget_info->guid . '">' . $budget_info->post_name . '</a> <span class="delete">X</span>';
                                    echo '<input type="hidden" name="budget_info_uploaded[]" value="' . $budget_info->ID . '"/>';
                                    echo '</li>';
                                }
                                echo '</ol>';
                            }

                            ?>
                        </fieldset>

                        <!-- STATUS -->
                        <fieldset>
                            <legend>Set the Proposal Status</legend>
                            <select name="status" id="status">
                                <?php if ('investigator' == $user_level) { ?>
                                    <option value="mede_draft" <?php if ('mede_draft' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Draft
                                    </option>
                                    <option value="submitted" <?php if ('submitted' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Submit
                                    </option>
                                <?php } elseif ('cmrg_lead' == $user_level) { ?>
                                    <option value="mede_draft" <?php if ('mede_draft' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Draft
                                    </option>
                                    <option value="submitted" <?php if ('submitted' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Submit
                                    </option>
                                    <option value="in_review" <?php if ('in_review' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>In Review
                                    </option>
                                    <option value="declined" <?php if ('declined' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Decline
                                    </option>
                                    <option value="contingent" <?php if ('contingent' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Contingent
                                    </option>
                                    <option value="admin_review" <?php if ('admin_review' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Approved for Admin Review
                                    </option>
                                <?php } elseif ('admin' == $user_level) { ?>
 									<option value="mede_draft" <?php if ('mede_draft' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Draft
                                    <option value="declined" <?php if ('declined' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Decline
                                    </option>
                                    <option value="contingent" <?php if ('contingent' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Contingent
                                    </option>
                                    <option value="admin_approved" <?php if ('admin_approved' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Approved
                                    </option>
                                    <option value="published" <?php if ('published' == $status) {
                                        echo 'selected="selected"';
                                    } ?>>Publish
                                    </option>
                                <?php } ?>
                            </select>


                        </fieldset>

                        <span class="pointer print">  <a href="?action=print">Print</a>  </span>

                        <button id="submit" class="submit">Save as Draft</button>

                        <?php wp_nonce_field('update_proposal', 'update_proposal_nonce'); ?>
                    </form>
                </div>
            </div>


        <?php endwhile; ?>
        <?php if (('cmrg_lead' == $user_level) || ('investigator' == $user_level)) {
            if (get_current_user_id() == get_the_author_meta('ID') && 'submitted' == $status) { ?>

                <script>
                    jQuery('#addProposal textarea').attr('disabled', 'disabled');
                </script>

            <?php }
        } ?>
    </main>

<?php else:
    // print("<script>window.location='" . home_url() . "'</script>");

endif; // ends $is_author = $proposal->is_author( get_current_user_id(), get_the_ID() );
    ?>

<?php else:
    // print("<script>window.location='" . home_url() . "'</script>");

endif; // ends if ($user_level != 'no_access' || $user_level != 'public') ?>
