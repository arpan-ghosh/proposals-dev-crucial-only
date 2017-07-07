<?php
/**
 * content-proposal-add
 * Add proposal view
 * now with EULA and Metals front end change
 * And back-end database changed
 */
global $user_level;

if ($user_level != 'no_access' || $user_level != 'public'): ?>


    <?php

    $errors = array();
    $msg = array();
    $error = 0;
    $extended_tech_images = array();
    $pub_images = array();
    $userdata = wp_get_current_user();
    $current_user_meta = get_user_meta($userdata->ID);
    $proposal_authors = array();

    if ('POST' == $_SERVER['REQUEST_METHOD']):

        if (isset($_POST['create_proposal_nonce']) && wp_verify_nonce($_POST['create_proposal_nonce'], 'create_proposal')) :
            $proposal = new Proposal();
            $proposal->create();


            if (('in_review' == $_POST['status']) || ('submitted' == $_POST['status'])) {
                // check if the status is set to in_review or submitted if so, send an email to let the CMRGs know. The array for the recipients is returned by function mede_construct_cmrg_lead_email_array().

                // Build the email out
                $subject = 'A proposal in your group has been marked as submitted, or in review.';
                $message = "View the proposal here: " . get_permalink($proposal->get_new_proposal_id());

                // you've got mail!
                wp_mail(mede_construct_cmrg_lead_email_array(get_current_user_id()), $subject, $message);

            } else {
                // Build the email out
                $subject = 'Your MEDE Proposal has been created.';
                $message = "View your proposal here: " . get_permalink($proposal->get_new_proposal_id());

                // you've got mail!
                echo $subject;
                echo $message;


                // wp_die();
                wp_mail($userdata->user_email, $subject, $message);
            }
            if ('investigator' == $user_level || 'cmrg_lead' == $user_level) {
                ?>
                <script>
                    // window.location = "<?php echo home_url() ?>?added=true&status=<?php echo $_POST['status']; ?>";
                </script>
                <?php
            }
        endif;
    endif;
    ?>

    <div class="proposal container">
        <div class="proposal__wrapper">
            <form id="addProposal" method="post" action="" enctype="multipart/form-data">

                <fieldset id="general">
                    <legend>General</legend>

                    <!-- TASK TITLE -->
                    <div>
                        <label for="task_title"><?php _e('Task Title', 'mede') ?></label>
                        <input type="text" name="task_title" id="task_title" value="<?php if (!empty($_POST['task_title'])) {
                            echo $title;
                        } ?>" data-validation="required"/>
                    </div>

                    <!-- TASK NUMBER -->
                    <?php if ('admin' == $user_level || 'cmrg_lead' == $user_level) { ?>
                        <div>
                            <label for="task_number">Task Number</label>
                            <input type="text" name="task_number" id="task_number" data-validation="number" value="<?php if (!empty($_POST['task_number'])) {
                                echo $task_number;
                            } ?>"/>
                        </div>
                    <?php }; ?>


                    <!-- CMRG GROUP -->
                    <div>
                        <label for="cmrg_group">Collaborative Materials Research Group (CMRG)</label>
                        <select name="cmrg_group" id="cmrg_group">
                            <option value="metals">Metals</option>
                            <option value="ceramics">Ceramics</option>
                            <option value="composites">Composites</option>
                            <option value="other">Other/Integrative</option>
                        </select>
                    </div>

                    <!-- CTRG -->
                    <div>
                        <label for="ctrg">Collaborative Technical Research Group (CTRG)</label>
                        <select name="ctrg" id="ctrg">
                            <option value="experimental">Experimental</option>
                            <option value="modeling">Modeling</option>
                            <option value="processing">Processing</option>
                            <option value="other">Other</option>
                        </select>
                    </div>


                    <!-- PERIOD OF PERFORMANCE -->
                    <div>
                        <label for="period_of_performance">Period Performance</label>
                        <select name="period_of_performance" id="period_of_performance">
                            <option value=""></option>
                            <option value="01_01_2018_to_12_31_2019">1/1/18 – 12/31/19</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- CONTINUING TASK -->
                    <div>
                        <label for="continuing_task">Continuing Task</label>
                        <select name="continuing_task" id="continuing_task">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                            <option value="new_related">New Task Related to Existing Task</option>
                        </select>

                        <div id="task_related_to" class="task-related hide">
                            <label for="task_related_to">Existing task this proposal is related to:</label>
                            <input type="text" name="task_related_to" id="task_related_to" value="<?php if (!empty($_POST['task_related_to'])) {
                                echo $task_related_to;
                            } ?>"/>
                        </div>
                    </div>
                </fieldset>

                <!-- INVESTIGATORS -->
                <fieldset>
                    <legend>Investigator(s) and Co-PI(s)</legend>

                    <div class="repeat">
                        <table class="wrapper" width="100%">
                            <thead>
                            <tr>
                                <td width="10%" colspan="4"><span class="add pointer">Add</span></td>
                            Please add co-PIs here. All CMEDE researchers directly affiliated with JHU are listed under "collaborator".
                            </tr>
                              <br>
                            </thead>
                            <br>
                            <tbody class="container">
                            <tr class="template row">
                                <td><span class="pointer move"><i class="icon-cursor-move-two"></i> Move</span></td>
                                <td>
                                    <dl>
                                        <dt>Co-PI</dt>
                                        <dd>
                                            <select name="collaborator[][collaborator]" id="collaborator_id">
                                                <option></option>
                                                <?php mede_display_collaborators_dropdown(); ?>
                                            </select>
                                        </dd>

                                        <dt>Co-PI Role</dt>
                                        <dd>
                                            <select name="collaborator[][collaborator_position]" id="collaborator_position">
                                                <option value="task_lead">Investigator</option>
                                                <option value="arl_collaborator">ARL Collaborator</option>
                                                <option value="postdoc">Postdoc</option>
                                                <option value="graduate_student">Graduate Student</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </dd>

                                        <dt>Phone</dt>
                                        <dd><input type="text" name="collaborator[][phone]"></dd>
                                    </dl>

                                </td>
                                <td><span class="pointer remove"><i class="icon-remove"></i> Remove</span></td>
                            </tr>
                            <tr class="row">
                                <td><span class="pointer move"><i class="icon-cursor-move-two"></i> Move</span></td>
                                <td>
                                    <dl>
                                        <dt>Co-PI</dt>
                                        <dd>
                                            <select name="collaborator[][collaborator]" id="collaborator_id">
                                                <option></option>
                                                <?php mede_display_collaborators_dropdown(get_current_user_id()); ?>
                                            </select>
                                        </dd>

                                        <dt>Co-PI Role</dt>
                                        <dd>
                                            <select name="collaborator[][collaborator_position]" id="collaborator_position">
                                                <option value="task_lead">Investigator</option>
                                                <option value="arl_collaborator">ARL Collaborator</option>
                                                <option value="postdoc">Postdoc</option>
                                                <option value="graduate_student">Graduate Student</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </dd>

                                        <dt>Phone</dt>
                                        <dd><input type="text" name="collaborator[][phone]"></dd>
                                    </dl>

                                    <dl class="hide collaborator_position_other">
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
                                    </dl>
                                </td>
                                <td><span class="pointer remove"><i class="icon-remove"></i> Remove</span></td>
                            </tr>

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
                                <td width="10%" colspan="4"><span class="add pointer">Add</span></td> 
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
                            <tr class="row">
                                <td><span class="pointer move"><i class="icon-cursor-move-two"></i> Move</span></td>
                                <td>
                                    <dl>
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
                                    </dl>
                                </td>
                                <td><span class="pointer remove"><i class="icon-remove"></i> Remove</span></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
    
                </fieldset>

                <fieldset>
                    <legend>Research Summary</legend>

                    <label for="science_objective">
                    <span class="groups_text">Problem Statement, Scientific Objectives, and Scientific Needs <br/><span>State the key aspect(s) and scientific objective(s) of the task. </span></span>
                    
                    <span class="metals_text" style="display: none;" >Metals Objectives</span>

                    </label>

                    <textarea name="science_objective" id="science_objective" cols="30" rows="10"  data-maxlength="750"></textarea>

                    <label for="science_objective_importance">
                        Importance of These Science Objectives<br/><span>Why do we need to achieve these objectives, relative to the problem statement? </span></label>
                    <textarea name="science_objective_importance" id="science_objective_importance" cols="30" rows="10" data-maxlength="750"></textarea>

                    <label for="state_of_art_capabilities">State-of-the-Art/Capabilities In This Subject Area<br/><span>What has already been done here or elsewhere?</span></label>
                    <textarea name="state_of_art_capabilities" id="state_of_art_capabilities" cols="30" rows="10"  data-maxlength="750"></textarea>

                    <label for="summary_of_research_strategy">Summary of Research Strategy<br/><span>Describe how you will meet your scientific objectives.</span></label>
                    <textarea name="summary_of_research_strategy" id="summary_of_research_strategy" cols="30" rows="10"  data-maxlength="750"></textarea>
                </fieldset>
                    <script type="text/javascript">
                      jQuery(document).ready(function () {
                        jQuery("[name=cmrg_group]").change(function () {
                          var cmrg_group = jQuery(this).val();
                          if(cmrg_group == "metals") {
                            jQuery(".metals_text").show();
                            jQuery(".groups_text").hide();
                          }
                          else {
                            jQuery(".groups_text").show();
                            jQuery(".metals_text").hide();
                          }
                        }); 
                      });
                    </script>
                <fieldset>
                    <legend>Extended Technical Description</legend>
                    <label for="extended_tech_description">This description should include how this task ties into your CMRG Problem Statement, Scientific Objectives and Scientific Needs.</label>
                    <textarea name="extended_tech_description" id="extended_tech_description" cols="30" rows="20"  data-maxlength="4000"></textarea>
                    <label for="extended_tech_upload_basic">Include one image; upload image file (file format: *jpeg,
                        .jpg, .png)</label>
                    <input type="file" name="extended_tech_upload_basic" class='extended_tech_upload_basic'/>
                </fieldset>

                <fieldset>
                    <! -- SCIENCE DELIVERABLE -->
                    <legend>Science Goals/Expected Progress</legend>

                    <label for="progress_by_mach_2016">What do you expect to have done by the 2018 Fall Meeting?<br/><span>Please list in bullet points.</span></label>
                    <textarea name="progress_by_mach_2016" id="progress_by_mach_2016" cols="30" rows="10"></textarea>

                    <label for="progress_by_fall_2016">What do you expect to have done by the 2019 MEDE Fall Meeting?  Describe in terms of milestones and decision points.</span></label>
                    <textarea name="progress_by_fall_2016" id="progress_by_fall_2016" cols="30" rows="10"></textarea>

                </fieldset>

                <fieldset>
                    <legend>Related Tasks and Intertask Collaborations</legend>
                    <!-- RELATED TASKS: PEOPLE -->
                    <label for="related_tasks_people">Person(s) with whom you are collaborating within your group and the CMRG:</label>
                    <textarea name="related_tasks_people" id="related_tasks_people" cols="30" rows="10"></textarea>

                    <!-- RELATED TASKS: DESCRIPTION -->
                    <label for="related_tasks_description">Describe Collaborative Exchanges:</label>
                    <textarea name="related_tasks_description" id="related_tasks_description" cols="30" rows="10"></textarea>

                    <hr/>

                    <!-- RELATED TASKS: PEOPLE -->
                    <label for="related_tasks_people2">Person(s) with whom you are collaborating within your group and the CMRG:</label>
                    <textarea name="related_tasks_people2" id="related_tasks_people2" cols="30" rows="10"></textarea>

                    <!-- RELATED TASKS: DESCRIPTION -->
                    <label for="related_tasks_description">Describe Collaborative Exchanges:</label>
                    <textarea name="related_tasks_description2" id="related_tasks_description2" cols="30" rows="10"></textarea>
                </fieldset>

                <fieldset>
                    <legend>Codes, Tools and Data Management</legend>
                    <!-- CODES -->
                    <label for="codes_tools">Briefly describe the (i) codes (if any), (ii) tools (if any), and (iii)
                        data (type and magnitude, e.g. images, text files, corresponding sizes) that will be created
                        during this task.</label>
                    <textarea name="codes_tools" id="codes_tools" cols="30" rows="10"></textarea>

                    <!-- MANAGEMENT -->
                    <label for="code_tools_management">Briefly describe where these (i) codes (if any) and (ii) data are
                        stored and backed-up. Describe your approach to sharing your codes, data and knowledge with the
                        Consortium and ARL. Specifically, describe to whom materials are transferred and for what purpose.</label>
                    <textarea name="code_tools_management" id="code_tools_management" cols="30" rows="10"></textarea>

                    
                                      <!-- EULA #1 -->
                                        <p> It is expected that all relevant data and publications generated during MEDE-funded research will be uploaded to the MEDE Document and Data Sharing Infrastructure (DDSI) servers beginning in 2018.  In particular, the primary data associated with any submitted publication that acknowledges MEDE support should be uploaded to the DDSI servers and thus made available to all MEDE collaborators. Specific instructions for accessing and loading data to the DDSI servers will be provided at the MEDE Fall Meeting.  </p>
                                        <br>
                                            <br>
                                            MEDE tasks that do not adhere to this expectation could have their funding impacted.
                                            <br>
                                            <br>
                                            The PI hereby acknowledges this expectation: Select YES or NO: 
  <label><input type="radio" name="terms" value="yes">Yes</label>
  <label><input type="radio" name="terms" value="no">No</label> 
                                        
                                      <br>
                                      <!-- EULA #2 -->
                                    
                                      <!-- POINT OF CONTACT -->
                    <label for="point_of_contact">Who is your point of contact for responding to requests related to
                        your codes, tools and data? (Name and email.)</label>
                    <textarea name="point_of_contact" id="point_of_contact" cols="30" rows="10"></textarea>
                                                    
                            </fieldset>
                            
                            <fieldset>
                    <legend>Transitions</legend>

                    <!-- TRANSITIONS -->
                    <label for="transitions">In the context of your CMRG Problem Statement, Scientific Objectives, and Scientific Needs, describe what transitions (codes, tools, data, knowledge, and materials) will be transitioned to (i) within your CMRG, (ii) to other CMRGs, and (iii) to ARL.</label>
                    <textarea name="transitions" id="transitions" cols="30" rows="10"></textarea>

                </fieldset>

                <fieldset>
                    <legend>Publications, Presentations, and Patents</legend>

                    <!-- PUBLICATIONS -->
                    <label for="publications_resultant">Within the context and timeframe for BPP FY16-17 (CY16-17), list the relevant publications, presentations, and patents that have resulted from your MEDE efforts.</label>
                    <textarea name="publications_resultant" id="publications_resultant" cols="30" rows="10"></textarea>

                   
                </fieldset>

                <fieldset>
                    <legend>Budget Information</legend>
                    <!-- DOWNLOAD BUDGET FORM -->
                    <label for="budget_info_upload"> <strong>Instructions:</strong>
                        <a target="_blank" href="<?php echo get_stylesheet_directory_uri() . '/assets/MEDE_BPP_FY16-17_(CY16-17)_Task_Proposal_Budget_Guidelines_v2.0.pdf'; ?>">Review
                            the FY16-17 (CY16-17) budget guidelines</a>, then
                        <a href="<?php echo get_stylesheet_directory_uri() . '/assets/MEDE-BPP-FY18-19-worksheet v1.xlsx'; ?>">download
                            this budget form</a>, save it as a .pdf or .xlsx, and upload it using the button below.<br/>
                        <br/>
                        <!-- UPLOAD THE FILE -->Include file(s); upload only a .pdf or .xlsx format file</label>
                    <input type="file" name="budget_info_upload[]" id="budget_info_upload" class="budget_info_upload"  multiple="multiple"/>
                </fieldset>

                <!-- STATUS -->
                <fieldset>
                    <legend>Set the Proposal Status</legend>
                    <select name="status" id="status">
                        <?php echo $user_level; ?>
                        <?php if ('investigator' == $user_level) { ?>
                            <option value="mede_draft">Draft</option>
                            <option value="submitted">Submit</option>
                        <?php } elseif ('cmrg_lead' == $user_level) { ?>
                            <option value="mede_draft">Draft</option>
                            <option value="submitted">Submit</option>
                            <option value="in_review">In Review</option>
                            <option value="decline">Decline</option>
                            <option value="contingent">Contingent</option>
                            <option value="admin_review">Approved for Admin Review</option>
                        <?php } elseif ('admin' == $user_level) { ?>
                            <option value="mede_draft">Draft</option>
                            <option value="decline">Decline</option>
                            <option value="contingent">Contingent</option>
                            <option value="approved">Approved</option>
                        <?php } ?>
                    </select>
                </fieldset>


                <!--                <span id="printme" class="print pointer">Print</span>-->

                <button id="submit" class="submit">Save as Draft</button>
                <?php wp_nonce_field('create_proposal', 'create_proposal_nonce'); ?>
            </form>
        </div>
    </div>

<?php else: ?>
    <?php wp_redirect(get_home_url());
    exit; ?>
<?php endif; ?>