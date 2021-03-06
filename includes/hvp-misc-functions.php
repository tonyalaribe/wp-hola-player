<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Misc Functions
 *
 * All misc functions handles to
 * different functions
 *
 * @package True Pundit Video Player
 * @since 1.0.0
 */

/**
 * Return mime type from url
 *
 * @package True Pundit Video Player
 * @since 1.0.0
 */

function hvp_get_mimetype($file) {
    if(empty($file))
        return 'video/mp4';
    $replaces = array('https://', 'https://www.', 'http://', 'http://www.', 'www.');
    $file = str_replace($replaces, '', $file);
    $file = str_replace('/', '.', $file);
   
    $file = strtolower($file);
    $extension = explode('.', $file);
    $mimes = hvp_mimelist();

    foreach ($extension as $value) {
        if(isset($mimes[$value]))
            return $mimes[$value];
    }

    $youtube = array('youtube', 'com');
    $vimeo = array('vimeo', 'com');

    // Check if youtube url
    if(count(array_intersect($extension, $youtube)) == count($youtube))
        return 'video/youtube';

    // Check if vimeo url
    if(count(array_intersect($extension, $vimeo)) == count($vimeo))
        return 'video/vimeo';

    return 'video/mp4';
}

function hvp_mimelist() {
    return array(
        'flv' => 'video/x-flv',
        'mp4' => 'video/mp4',
        'm3u8' => 'application/x-mpegurl',
        'ts' => 'video/MP2T',
        '3gp' => 'video/3gpp',
        'mov' => 'video/quicktime',
        'avi' => 'video/x-msvideo',
        'wmv' => 'video/x-ms-wmv',
        'avs' => 'video/avs-video',
        'm3u' => 'audio/x-mpegurl',
        'f4m' => 'application/adobe-f4m'
    );
}

function hvp_user_details() {
    $user = wp_get_current_user();
    $url = get_bloginfo('url');
    return array(
        'email' => $user->user_email,
        'name' => $user->display_name,
        'site' => $url,
    );
}


function hvp_build_video_tag($attr) {
    $url = $attr['url'];
    $width = $attr['width'];
    $height = $attr['height'];
    $muted = $attr['muted'];
    $ytcontrol = $attr['ytcontrol'];
    //$adjscode = $attr['adjscode'];

    $mime_type = hvp_get_mimetype($url);


    $last_script = 'hvp_video_script';
    wp_enqueue_script($last_script);
    wp_enqueue_script('hvp_video_script2');

    $skin = 'vjs-default-skin';
    if (!empty($width) && strpos($width, '%') == false
        && strpos($width, 'px') == false) {
        $width = $width.'px';
    }

    $opts = array();
    // $opts['player'] = "document.getElementById('$res_class')";
    $opts['sources'] = "[{ type: '$mime_type', src: '$url'}]";

    if (!empty($muted))
        $opts['muted'] = $muted;
    if (!empty($attr['autoplay']))
        $opts['autoplay'] = $attr['autoplay'];
    if (!empty($attr['loop']))
        $opts['loop'] = $attr['loop'];
    if (!empty($attr['controls']))
        $opts['controls'] = $attr['controls'];
    if ($muted)
        $opts['volume'] = 'false';

    if($mime_type == 'video/youtube') {
        $last_script = 'hvp_youtube_video_script';
        wp_enqueue_script($last_script);
        $videojs_opts = "techOrder: ['youtube']";
        if($ytcontrol === true || $ytcontrol === 'true') {
            $videojs_opts .= ", youtube: { ytControls: 2 }";
            unset($opts['controls']);
        }
        $opts['videojs_options'] = "{ $videojs_opts }";
        unset($attr['adtagurl']);
    } elseif ($mime_type == 'video/vimeo') {
        $last_script = 'hvp_vimeo_video_script';
        wp_enqueue_script($last_script);
        $videojs_opts = "techOrder: ['vimeo']";
        if($ytcontrol === true || $ytcontrol === 'true') {
            $videojs_opts .= ", vimeo: { ytControls: 2 }";
            unset($opts['controls']);
        }
        $opts['videojs_options'] = "{ $videojs_opts }";
        unset($attr['adtagurl']);
    }

    if ($attr['adtagurl']) {
        // IMA ADS SDK
        $last_script = 'hvp_ima_ads_sdk_script';
        wp_enqueue_script($last_script);
        $adtagurl = html_entity_decode($attr['adtagurl']);
        $opts['ads'] = "{ adTagUrl: '$adtagurl' }";
    }

    $opt_string = implode(', ', array_map(
        function ($v, $k) { return "$k: $v"; },
        $opts, array_keys($opts)));
    // wp_add_inline_script($last_script, "window.hola_player({ $opt_string });");

    ob_start();
    ?>
    <!-- <div class="hvp-video hvp-content-video">
      <video id="<?php print $res_class?>" data-id="<?php print $res_class?>"
        class="video-js <?php print "$skin $res_class {$attr['class']}"; ?>"
        preload="<?php print $attr['preload']; ?>" width="<?php print $width?>"
        <?php print $muted ? "muted" : ""?>
        height="<?php print $height?>" poster="<?php print $attr['poster'];?>">
            <p class="vjs-no-js"><?php _e('To view this video please enable JavaScript, and consider upgrading to a web browser that', HVP_TEXTDOMAIN) ?> <a href="http://videojs.com/html5-video-support/" target="_blank"><?php _e('supports HTML5 video', HVP_TEXTDOMAIN) ?></a></p>
      </video>
    </div> -->

    <section>
  
      <video class="video-js vjs-skin-flat-red vjs-16-9" id="<?php print $attr['video_id'];?>" style="max-width: 100%;"
          controls
          preload="<?php print $attr['preload']; ?>"
        width="<?php print $width?>"
          <?php if ($muted=="true"){
        print "muted";
        }
        ?>
        height="<?php print $height?>" 
        poster="<?php print $attr['poster'];?>"
          data-setup='{
            "plugins": {
              "vastClient": {
                "adTagUrl": "<?php print $attr['adtagurl'] ?>",
                "adCancelTimeout": 8000,
                "adsEnabled": true
                }
              }
            }'>
        <source src="<?php  print $attr['url']; ?>" type="<?php print $mime_type ?>"></source>
        
        <p class="vjs-no-js">
          To view this video please enable JavaScript, and consider upgrading to a
          web browser that
          <a href="http://videojs.com/html5-video-support/" target="_blank">
            supports HTML5 video
          </a>
        </p>
      </video>
    
    </section>
    <section style="display: none">
          <div class="tp_overlay_ad" id="ad" style="margin-top:2%">
            <?php  print get_option('tp-default-js-ad-code'); ?>
        </div>
    </section>

    <style type="text/css">
        .tp_overlay_ad {
            max-height: 40%;
            width: auto;
            margin:0 auto;
            text-align: center;
            margin-top:2%;
        }
    </style>
        
      <script type="text/javascript">
          var player = videojs('<?php print $attr['video_id'];?>');

          var ModalDialog = videojs.getComponent('ModalDialog');

          var modal = new ModalDialog(player, {

            // We don't want this modal to go away when it closes.
            temporary: false
          });

          var elem = modal.contentEl()

          console.log(elem)

          player.addChild(modal);

          player.on('pause', function() {
            modal.content(document.getElementById("ad"));
            modal.open();
          });

          player.on('play', function() {
            //modal.content(document.getElementById("ad"));
            modal.close();
          });

          modal.on('modalclose', function() {
            player.play();
          });

      </script>

<?php
    $content = ob_get_clean();
    return $content;
  }
?>
