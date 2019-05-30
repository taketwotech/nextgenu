<?php

// include Moodle config
if (!@include_once(__DIR__.'/../../config.php')) {
    include_once('/var/www/remui.local/html/v35/config.php');
}

global $DB, $OUTPUT, $CFG, $USER;

$response = \theme_remui\utility::get_remui_announcemnets();

// get update info
$updateinfo = array();
if (isset($response['updateinfo']) && !empty($response['updateinfo'])) {
    $updateinfo = $response['updateinfo'];
}

// get remui installed status
$pm = \core_plugin_manager::instance();
$currentremui = $pm->get_plugin_info('theme_remui')->release;
$currentmoodle = substr($CFG->release, 0, 3);
?>
<style>
  input.star { display: none; }
  label.star {
    float: right;
    padding: 0 10px;
    margin: 0;
    font-size: 22px;
    color: #999;
    transition: all .2s;
  }

  input.star:checked ~ label.star:before {
    content: '\f005';
    color: #Fb4;
    /* transition: all .2s; */
  }
  input.star-1:checked ~ label.star:before { color: #F62; }
  input.star-2:checked ~ label.star:before { color: #F92; }
  /* label.star:hover { transform: scale(1.1); } */
  label.star:before {
    content: '\f006';
    font-family: Font Awesome;
  }
</style>

<!-- updates info section -->
<div class="row update-info">
  <div class="col-12 col-md-12 my-35">
    <?php
        $lcontroller = new \theme_remui\controller\license_controller();
        $getlidatafromdb = $lcontroller->getDataFromDb();
    if ('available' == $getlidatafromdb) {
        if (!empty($updateinfo) && isset($updateinfo[$currentmoodle])) {
            if ($updateinfo[$currentmoodle]['version'] > $currentremui) {
                echo "<p class='lead text-center alert alert-info'>".get_string('newupdatemessage', 'theme_remui')."</p>
                <h2 class='font-size-48 text-center'>v{$updateinfo[$currentmoodle]['version']}</h2>
                <p class='text-center font-size-14'>".get_string('currentversionmessage', 'theme_remui')."<cite title='Source Title' class='h5 font-weight-700'>v{$currentremui}</cite>&nbsp;&nbsp; <a href='{$updateinfo[$currentmoodle]['downloadlink']}' class='badge badge-info badge-pill'>".get_string('downloadupdate', 'theme_remui')."</a></p>";
            } else {
                echo "<p class='lead text-center alert alert-success'>".get_string('latestversionmessage', 'theme_remui')."</p>
                <h2 class='font-size-48 text-center'>v{$currentremui}</h2>";
            }
        }
    } else {
        echo "<p class='lead text-center'>".get_string('licensenotactive', 'theme_remui')."</p>";
    }
        ?>
    </div>
    <div class="col-12 col-md-6 my-35">
      <!-- <form class='form-horizontal' id="remuifeedbackform"> -->
       <!-- <div class="form-group">
          <div class="stars d-inline-block">
            <span class="btn btn-pure pl-0"><?php echo get_string('rateremui', 'theme_remui'); ?></span>
            <input class="star star-5" id="star-5" value="5" type="radio" name="rrating"/>
            <label class="star star-5" for="star-5"></label>
            <input class="star star-4" id="star-4" value="4" type="radio" name="rrating"/>
            <label class="star star-4" for="star-4"></label>
            <input class="star star-3" id="star-3" value="3" type="radio" name="rrating"/>
            <label class="star star-3" for="star-3"></label>
            <input class="star star-2" id="star-2" value="2" type="radio" name="rrating"/>
            <label class="star star-2" for="star-2"></label>
            <input class="star star-1" id="star-1" value="1" type="radio" name="rrating"/>
            <label class="star star-1 pl-0" for="star-1"></label>
          </div>
        </div> -->
        <!-- <div class='form-group'>
          <input type='text' class='form-control rfullname' placeholder="<?php // echo get_string('fullname', 'theme_remui'); ?>" value="<?php // echo @$USER->firstname.' '.@$USER->lastname; ?>">
        </div>

        <div class='form-group'>
          <input type='email' class='form-control remail' placeholder="<?php // echo get_string('email', 'theme_remui'); ?>" value="<?php // echo $USER->email; ?>">
        </div> -->
        
       <!-- <div class='form-group'>
          <textarea class='form-control rfeedback' placeholder="<?php echo get_string('providefeedback', 'theme_remui'); ?>"></textarea>
        </div> -->

        <div class='form-group'>
          <!-- <button type='button' id="sendfeedback" class='btn btn-primary float-left'"><?php echo get_string('sendfeedback', 'theme_remui'); ?> <i class="fa fa-spinner fa-spin feedbacki d-none" aria-hidden="true"></i></button>
          <span class="btn btn-pure feedbackm" style="position: absolute;"></span> -->
        </div>
      <!-- </form> -->
    </div>
</div>
<hr />
<!-- announcements section -->
<?php
echo "<div class='row mx-35 mt-60 announcements'>";
echo "<div class='col-12'>";

if (isset($response['announcements']) && !empty($response['announcements'])) {
    echo "<h2 class='h3 mb-25'>".get_string('recentnews', 'theme_remui')."</h2>";

    echo '<div class="card-columns">';
    foreach ($response['announcements'] as $anc) {
        if (!empty($anc['image'])) {
            echo "<div class='card' style='background: #f1f4f5;'>
                <img class='card-img-top w-full' src='{$anc['image']}'>
  	                <div class='card-block'>";
            if (!empty($anc['link'])) {
                echo "<h4 class='card-title'><a href='{$anc['link']}' target='_blank' class='grey-800'>{$anc['title']}</a></h4>";
            } else {
                echo "<h4 class='card-title grey-800'>{$anc['title']}</h4>";
            }
            if (!empty($anc['excerpt'])) {
                echo "<p class='card-text'>{$anc['excerpt']}</p>";
            }
            echo "</div>
  	              </div>";
        } else {
            echo "<div class='card card-block card-inverse card-{$anc['type']} text-center p-15'>
                  <blockquote class='blockquote cover-quote card-blockquote'>";
            if (!empty($anc['excerpt'])) {
                echo "<p>{$anc['excerpt']}</p>";
            }

            if (!empty($anc['link'])) {
                echo "<footer>
  	                    <small>
  	                      Read more
  	                      <cite title='Here'><a href='{$anc['link']}' target='_blank' class='text-white font-size-16 font-weight-600'>Here</a></cite>
  	                    </small>
  	                  </footer>";
            }
            echo "</blockquote>
  	              </div>";
        }
    }
    echo '</div>';
}

echo "</div>
</div>";
