<?php

if (!defined('ABSPATH')) exit;

/**
 * Program screening template
 *
 * Input:
 * $data: {
 *   "screening": { ... }
 * }
 */

$s                = $data->screening;
$id               = $data->screen_id;
$time_to_reload   = $data->time_to_reload;
$main_movie_index = $data->main_movie_index;
$language         = $data->language;

if (!(is_null($s))) :
?>
<style type="text/css">
@import url('https://fonts.googleapis.com/css?family=Lato:300,400,700,900');

body {
    color:black;
    background-color:white;
    background-repeat:no-repeat;
    width:100%;
    height:100%;
}

.wp-site-blocks, .has-global-padding {
    padding:0;
}

.heure_seance {
    background-color: lightgrey;
    position: relative;
}

h3 {
    font-family: Lato;
    color:#190201;
    font-size:120px;
    font-weight:700;
    position: relative;
    top: 7px;
    left: 5px;
    margin: 0 !important;
    padding:0 !important;
}

.start_in {
    background-color: aquamarine;
    top: 0px;
    position:relative;
}

h2 {
    font-family: Lato;
    color:#333;
    font-size:70px;
    font-weight:700;
    word-spacing:54px;
    position:relative;
    top: -80px;
    left: 73px;
    margin: 0 !important;
    padding:0 !important;
}

.cadre_film {
    background-color:darkcyan;
    background-repeat:no-repeat;
    position:relative;
    top: 0px;
    left: 0px;
    text-align:center;
    font-family: Lato;
    color:snow;
    font-size:60px;
    font-weight:700;
    text-transform: uppercase;
    padding-top:15px;
    padding-left:60px !important;
}

.affiche {
    background-image: url("<?php echo esc_attr(tkt_img_proxy_url($s->movies()[$main_movie_index]->opaque()["posters"][0]["url"], 755, 885)) ?>");
    background-repeat:no-repeat;
    background-position: center;
    position:relative;
}

.noborder{
    border:0;
}

.hidden{
    display:none;
}

#age {
    top: 80px;
    left: 80px;
    color: #333;
    font-size: 60px;
}

#version {
    top: 80px;
    left: 80px;
    color: #333;
    font-size: 60px;
}

#countdown {
    top: 0px;
}
</style>

<?php endif; ?>

<style type="text/css">
#myVideo {
    position: fixed;
    right: 0;
    bottom: 0;
    min-width: 100%;
    min-height: 100%;
}
</style>

<table border="0" cellspacing="0" cellpadding="0" width="1920" height="1080" style="margin:0 !important; max-width:1920px !important;">
<?php if (!(is_null($s))) : ?>
    <tr class="noborder" height="80%">
        <td class="heure_seance" width="499" rowspan="2">
            <h3>
                Heure<br/><?php echo esc_html(wp_date('H:i',$s->start_at()->getTimestamp())); ?>
            </h3>
        </td>
        <td id="cadre_countdown" class="start_in" width="666" >
            <h2 id="countdown">
                Dans : 00h00
            </h2>
            <h3 id="age">
                Age : +<?php if(!empty($s->movies()[0]->opaque()["l_min_age"])) echo esc_html($s->movies()[0]->opaque()["l_min_age"]); else echo 0; ?></h3><h3 id="version"><?php echo esc_html($s->opaque()["version"]); ?>
            </h3>
        </td>
        <td class="affiche" width="755" ></td>
    </tr>
    <tr class="noborder">
        <th class="cadre_film" colspan="2">
            <?php echo esc_html($s->localized_title_or_original($language)) ?>
        </th>
    </tr>
<?php endif; ?>
</table>

<script>
    <?php if (!(is_null($s))) : ?>
        var countDownDate    = <?php echo esc_html($s->start_at()->getTimestamp()); ?> * 1000;
        var now              = <?php echo esc_html(time()); ?> * 1000;
        var reloadDate       = <?php echo esc_html($time_to_reload->getTimestamp()); ?> * 1000;
        var main_movie_index = <?php echo esc_html($data->main_movie_index); ?>;

        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get todays date and time
            now = now + 1000;

            // Find the distance between now an the count down date
            var distanceToBeginning = countDownDate - now;
            var distanceToReload = reloadDate - now;
            // Time calculations for days, hours, minutes and seconds
            var hours = Math.floor((distanceToBeginning % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.ceil((distanceToBeginning % (1000 * 60 * 60)) / (1000 * 60));

            //Add a 0 before numbers with only one digit
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (hours < 10) {
                hours = "0" + hours;
            }

            // Output the result in an element with id="demo"
            document.getElementById("countdown").innerHTML = "Dans : " + hours + "h" + minutes;

            // If the count down is over, write some text 
            if (distanceToBeginning < 0) {
                document.getElementById("cadre_countdown").className = "";
                document.getElementById("countdown").className = " hidden";
            }

            if ( distanceToReload < 0 ) {
                clearInterval(x);
                window.location.reload(true);
            }
        }, 1000);
    <?php else : ?>
        var now        = <?php echo esc_html(time()); ?> * 1000;
        var reloadDate = <?php echo esc_html($time_to_reload->getTimestamp()); ?> * 1000;

        var interval = setInterval(function() {
            now = now + 1000;
            var distanceToReload = reloadDate - now;
            if ( distanceToReload < 0 ) {
                clearInterval(interval);
                window.location.reload(true);
            }
        }, 1000);
    <?php endif; ?>
</script>
