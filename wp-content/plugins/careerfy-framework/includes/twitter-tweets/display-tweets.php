<?php

/**
 * Get twitter authentication.
 *
 * @param string $id twitter username.
 * @param number $max_tweets number of tweets.
 */
function get_auth($id, $max_tweets) {
    global $careerfy_framework_options;
    $include_rts = true; // include retweets is set to true by default, if you don't want to include retweets set this to false.
    $exclude_replies = true; // Replies are not displayed by default.  If you wish to change this set this to false.

    $jobsearch_plugin_options = get_option('jobsearch_plugin_options');
    
    $consumer_key = isset($careerfy_framework_options['careerfy-twitter-consumer-key']) ? $careerfy_framework_options['careerfy-twitter-consumer-key'] : '';
    $consumer_secret = isset($careerfy_framework_options['careerfy-twitter-consumer-secret']) ? $careerfy_framework_options['careerfy-twitter-consumer-secret'] : '';
    $user_token = isset($careerfy_framework_options['careerfy-twitter-access-token']) ? $careerfy_framework_options['careerfy-twitter-access-token'] : '';
    $user_secret = isset($careerfy_framework_options['careerfy-twitter-token-secret']) ? $careerfy_framework_options['careerfy-twitter-token-secret'] : '';
    
    if (isset($jobsearch_plugin_options['jobsearch-twitter-consumer-key']) && $jobsearch_plugin_options['jobsearch-twitter-consumer-key'] != '') {
        $consumer_key = $jobsearch_plugin_options['jobsearch-twitter-consumer-key'];
    }
    if (isset($jobsearch_plugin_options['jobsearch-twitter-consumer-secret']) && $jobsearch_plugin_options['jobsearch-twitter-consumer-secret'] != '') {
        $consumer_secret = $jobsearch_plugin_options['jobsearch-twitter-consumer-secret'];
    }
    if (isset($jobsearch_plugin_options['jobsearch-twitter-access-token']) && $jobsearch_plugin_options['jobsearch-twitter-access-token'] != '') {
        $user_token = $jobsearch_plugin_options['jobsearch-twitter-access-token'];
    }
    if (isset($jobsearch_plugin_options['jobsearch-twitter-token-secret']) && $jobsearch_plugin_options['jobsearch-twitter-token-secret'] != '') {
        $user_secret = $jobsearch_plugin_options['jobsearch-twitter-token-secret'];
    }

    require_once 'includes/tmhOAuth.php';

    $tmhOAuth = new tmhOAuth(array(
        'consumer_key' => ltrim(rtrim($consumer_key)),
        'consumer_secret' => ltrim(rtrim($consumer_secret)),
        'user_token' => ltrim(rtrim($user_token)),
        'user_secret' => ltrim(rtrim($user_secret)),
    ));
    $twitter_settings_arr = array(
        'count' => $max_tweets,
        'screen_name' => $id,
        'include_rts' => $include_rts,
        'exclude_replies' => $exclude_replies,
    );

    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), $twitter_settings_arr);

    $res_code = array(
        '200',
        '304',
    );
    if (in_array($code, $res_code)) {
        $data = $tmhOAuth->response['response'];
        return $data;
    } else {
        return $data = '500';
    }
}

/**
 * Twitter Cache.
 *
 * @param string $id twitter username.
 * @param number $max_tweets number of tweets.
 * @param number $time cache time.
 */
function cache_json($id, $max_tweets, $time) {
    $cache_dir = plugin_dir_path(__FILE__) . 'cache/';
    $cache = $cache_dir . $id . '.json'; // Twitter cache directory.
    $cache_folder = $cache_dir; // Twitter cache directory.
    if (!file_exists($cache)) {
        if (!file_exists($cache_folder)) {
            $cache_dir = mkdir($cache_folder);
            $cache_data = true;
        }
        if (!file_exists($cache)) {
            $cache_data = true;
        }
    } else {
        $cache_time = time() - filemtime($cache);
        if ($cache_time > 60 * absint($time)) {
            $cache_data = true;
        }
    }
    $tweets = '';

    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once ( ABSPATH . '/wp-admin/includes/file.php' );
        WP_Filesystem();
    }

    if (isset($cache_data)) {
        $data = get_auth($id, $max_tweets);
        if ($data != '500') {
            $cached = $wp_filesystem->put_contents($cache, $data);
        }
    }
    $tweets = json_decode($wp_filesystem->get_contents($cache), true);

    return $tweets;
}

/**
 * Date Difference.
 *
 * @param number $time1 Today time.
 * @param number $time2 Tweet publication time.
 * @param number $precision number of day/days precision.
 */
function tweetDateDiff($time1, $time2, $precision = 6) {
    if (!is_int($time1)) {
        $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
        $time2 = strtotime($time2);
    }
    if ($time1 > $time2) {
        $ttime = $time1;
        $time1 = $time2;
        $time2 = $ttime;
    }
    $intervals = array(
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second'
    );
    $diffs = array();
    foreach ($intervals as $interval) {
        $diffs[$interval] = 0;
        $ttime = strtotime('+1 ' . $interval, $time1);
        while ($time2 >= $ttime) {
            $time1 = $ttime;
            $diffs[$interval] ++;
            $ttime = strtotime('+1' . $interval, $time1);
        }
    }
    $count = 0;
    $times = array();
    foreach ($diffs as $interval => $value) {
        if ($count >= $precision) {
            break;
        }
        if ($value > 0) {
            if (1 !== intval($value)) {
                $interval .= 's';
            }
            $times[] = $value . ' ' . $interval;
            $count ++;
        }
    }
    return implode(', ', $times);
}

/**
 * Display tweets.
 *
 * @param string $id Twitter username.
 * @param string $style Tweet date time format.
 * @param number $max_tweets number of tweets.
 * @param number $max_cache_tweets cache time.
 * @param number $time tweets time.
 */
function careerfy_display_tweets($id, $style = '', $max_tweets = 10, $max_cache_tweets = 10, $time = 60, $tweets_view = '') {
    $tweets = cache_json($id, $max_tweets, $time);
    $twitter = '';

    if (!empty($tweets)) {

        if ($tweets_view == 'widget') {

            wp_enqueue_script('careerfy-slick-slider');
            
            $twitter .= '<div class="careerfy_twitter_widget_wrap">';
            $tweet_flag = 1;
            foreach ($tweets as $tweet) {

                $pub_date = $tweet['created_at'];
                $tweet_user_name = isset($tweet['user']['name']) ? $tweet['user']['name'] : '';
                $tweet_user_img = isset($tweet['user']['profile_image_url_https']) ? $tweet['user']['profile_image_url_https'] : '';
                $tweet_user_url = isset($tweet['user']['url']) ? $tweet['user']['url'] : '';

                $tweet = isset($tweet['text']) ? $tweet['text'] : '';

                $today = time();
                $time = substr($pub_date, 11, 5);
                $day = substr($pub_date, 0, 3);
                $date = substr($pub_date, 7, 4);
                $month = substr($pub_date, 4, 3);
                $year = substr($pub_date, 25, 5);
                $english_suffix = date('jS', strtotime(preg_replace('/\s+/', ' ', $pub_date)));
                $full_month = date('F', strtotime($pub_date));

                // pre-defined tags.
                $default = $full_month . $date . $year;
                $full_date = $day . $date . $month . $year;
                $ddmmyy = $date . $month . $year;
                $mmyy = $month . $year;
                $mmddyy = $month . $date . $year;
                $ddmm = $date . $month;

                // Time difference.
                $time_diff = tweetDateDiff($today, $pub_date, 1);

                // Turn URLs into links.
                $tweet = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\./-]*(\?\S+)?)?)?)@', '<a target="blank" title="$1" href="$1">$1</a>', $tweet);

                // Turn hashtags into links.
                $tweet = preg_replace('/#([0-9a-zA-Z_-]+)/', "<a target='blank' title='$1' href=\"http://twitter.com/search?q=%23$1\">#$1</a>", $tweet);

                // Turn @replies into links.
                $tweet = preg_replace("/@([0-9a-zA-Z_-]+)/", "<a target='blank' title='$1' href=\"http://twitter.com/$1\">@$1</a>", $tweet);

                $tw_date = '';
                if (isset($style)) {
                    if (!empty($style)) {

                        switch ($style) {
                            case 'eng_suff': {
                                    $tw_date = $english_suffix . '&nbsp;' . $full_month;
                                }
                                break;
                            case 'time_since';
                                {
                                    $tw_date = $time_diff . '&nbsp;ago';
                                }
                                break;
                            case 'ddmmyy';
                                {
                                    $tw_date = $ddmmyy;
                                }
                                break;
                            case 'ddmm';
                                {
                                    $tw_date = $ddmm;
                                }
                                break;
                            case 'full_date';
                                {
                                    $tw_date = $full_date;
                                }
                                break;
                            case 'default';
                                {
                                    $tw_date = $default;
                                }
                        } // end switch statement.
                    }
                    $when = ( 'time_since' === $style ) ? '' : __(' on ', 'careerfy-frame');
                    $tw_date = $when . $tw_date;
                }

                $twitter .= '<div class="twitter-slide-item"><p>' . $tweet . '</p><span>' . $tw_date . '</span></div>' . "\n";

                if ($max_cache_tweets <= $tweet_flag) {
                    break;
                }
                $tweet_flag ++;
            } //end of foreach.
            $twitter .= '</div>';
        } else {

            $twitter .= '<div class="careerfy-twitter-slider">';
            $tweet_flag = 1;
            foreach ($tweets as $tweet) {

                $pub_date = $tweet['created_at'];
                $tweet_user_name = isset($tweet['user']['name']) ? $tweet['user']['name'] : '';
                $tweet_user_img = isset($tweet['user']['profile_image_url_https']) ? $tweet['user']['profile_image_url_https'] : '';
                $tweet_user_url = isset($tweet['user']['url']) ? $tweet['user']['url'] : '';

                $tweet = isset($tweet['text']) ? $tweet['text'] : '';

                $today = time();
                $time = substr($pub_date, 11, 5);
                $day = substr($pub_date, 0, 3);
                $date = substr($pub_date, 7, 4);
                $month = substr($pub_date, 4, 3);
                $year = substr($pub_date, 25, 5);
                $english_suffix = date('jS', strtotime(preg_replace('/\s+/', ' ', $pub_date)));
                $full_month = date('F', strtotime($pub_date));

                // pre-defined tags.
                $default = $full_month . $date . $year;
                $full_date = $day . $date . $month . $year;
                $ddmmyy = $date . $month . $year;
                $mmyy = $month . $year;
                $mmddyy = $month . $date . $year;
                $ddmm = $date . $month;

                // Time difference.
                $time_diff = tweetDateDiff($today, $pub_date, 1);

                // Turn URLs into links.
                $tweet = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\./-]*(\?\S+)?)?)?)@', '<a target="blank" title="$1" href="$1">$1</a>', $tweet);

                // Turn hashtags into links.
                $tweet = preg_replace('/#([0-9a-zA-Z_-]+)/', "<a target='blank' title='$1' href=\"http://twitter.com/search?q=%23$1\">#$1</a>", $tweet);

                // Turn @replies into links.
                $tweet = preg_replace("/@([0-9a-zA-Z_-]+)/", "<a target='blank' title='$1' href=\"http://twitter.com/$1\">@$1</a>", $tweet);

                if ($tweet_flag == 1) {
                    $twitter .= '
                    <div class="careerfy-twitter-slide-layer">
                    <ul>';
                }

                $tw_date = '';
                if (isset($style)) {
                    if (!empty($style)) {

                        switch ($style) {
                            case 'eng_suff': {
                                    $tw_date = $english_suffix . '&nbsp;' . $full_month;
                                }
                                break;
                            case 'time_since';
                                {
                                    $tw_date = $time_diff . '&nbsp;ago';
                                }
                                break;
                            case 'ddmmyy';
                                {
                                    $tw_date = $ddmmyy;
                                }
                                break;
                            case 'ddmm';
                                {
                                    $tw_date = $ddmm;
                                }
                                break;
                            case 'full_date';
                                {
                                    $tw_date = $full_date;
                                }
                                break;
                            case 'default';
                                {
                                    $tw_date = $default;
                                }
                        } // end switch statement.
                    }
                    $when = ( 'time_since' === $style ) ? '' : __(' on ', 'careerfy-frame');
                    $tw_date = $when . $tw_date;
                }

                $twitter .= '
                <li>
                    <figure><a href="' . $tweet_user_url . '"><img src="' . $tweet_user_img . '" alt=""></a></figure>
                    <div class="careerfy-twitter-text">
                        <h5><a href="' . $tweet_user_url . '">' . $tweet_user_name . '</a></h5>
                        <small>' . $tw_date . '</small>
                        <span>' . $tweet . '</span>
                    </div>
                </li>' . "\n";

                if (fmod($tweet_flag, 3) == 0) {
                    $twitter .= '
                    </ul>
                    </div>
                    <div class="careerfy-twitter-slide-layer">
                    <ul>';
                }

                if ($max_cache_tweets == $tweet_flag) {
                    $twitter .= '
                    </ul>
                    </div>';
                }

                if ($max_cache_tweets <= $tweet_flag) {
                    break;
                }
                $tweet_flag ++;
            } //end of foreach.
            $twitter .= '</div>';
        }
    } else {
        $twitter .= '<p>' . __('No Tweets Found.', 'careerfy-frame') . '</p>';
    } //end if statement.
    
    
    $twitter = apply_filters('geek_twitter_tweets',$twitter,$tweets,$max_tweets,$time,$tweets_view,$id,$max_cache_tweets,$style);
    
    echo ($twitter);
}
