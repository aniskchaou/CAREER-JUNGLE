<?php

function jobsearch_upload_cand_port_img($Fieldname = 'file', $post_id = 0, $img_type = 'profile_img') {
    global $jobsearch_uploding_candimg, $jobsearch_download_locations;
    $jobsearch_download_locations = false;
    $jobsearch_uploding_candimg = true;
    //
    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

        add_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $upload_file = $_FILES[$Fieldname];

        $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $allowed_image_types = array(
            'jpg|jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        );

        $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));

        if (empty($status_upload['error'])) {

            $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';
            $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

            $folder_path = $wp_upload_dir['path'];

            $image = wp_get_image_editor($upload_file_path);

            if (!is_wp_error($image)) {
                $file_name = basename($file_url);

                $file_urls = array(
                    'path' => $upload_file_path,
                    'orig' => $file_url,
                );

                return $file_urls;
            }
        }

        remove_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);
    }

    return false;
}

function jobsearch_get_cand_portimg_url($candidate_id, $portfolio_img, $url_with_size = '') {
    if ($portfolio_img != '') {
        if (!is_numeric($portfolio_img)) {
            if ($url_with_size != '') {
                $attach_id = jobsearch_get_attachment_id_from_url($portfolio_img);
                $port_thumb_image = wp_get_attachment_image_src($attach_id, $url_with_size);
                $portfolio_img = isset($port_thumb_image[0]) && esc_url($port_thumb_image[0]) != '' ? $port_thumb_image[0] : $portfolio_img;
            } else {
                $portfolio_img = $portfolio_img;
            }
        } else {
            $post_imgs_parr = get_post_meta($candidate_id, 'jobsearch_portfolio_imgs_arr', true);
            if (isset($post_imgs_parr[$portfolio_img])) {
                $img_arr = $post_imgs_parr[$portfolio_img];
                
                if (isset($img_arr['path'])) {
                    
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    WP_Filesystem();
                    global $wp_filesystem;

                    $file_path = $img_arr['path'];
                    $file_url = $img_arr['orig'];
                    
                    $filename = basename($file_url);
                    $filetype = wp_check_filetype($filename, null);
                    $file_ext = isset($filetype['ext']) ? $filetype['ext'] : '';
                    
                    if (!file_exists($file_path)) {
                        $file_path = str_replace(get_site_url() . '/', ABSPATH, $file_url);
                    }
                    
                    $data = @$wp_filesystem->get_contents($file_path);
                    $portfolio_img = 'data:image/' . $file_ext . ';base64,' . base64_encode($data);
                }
            }
        }
        //
    }
    
    return $portfolio_img;
}

function jobsearch_get_cand_portimg_path($candidate_id, $portfolio_img) {
    if ($portfolio_img != '') {
        if (!is_numeric($portfolio_img)) {
            $attach_id = jobsearch_get_attachment_id_from_url($portfolio_img);
            if ($attach_id > 0 && get_post_type($attach_id) == 'attachment') {
                $file_path = get_attached_file($attach_id);
                $portfolio_img = $file_path;
            }
        } else {
            $post_imgs_parr = get_post_meta($candidate_id, 'jobsearch_portfolio_imgs_arr', true);
            if (isset($post_imgs_parr[$portfolio_img])) {
                $img_arr = $post_imgs_parr[$portfolio_img];
                
                if (isset($img_arr['path'])) {
                    
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    WP_Filesystem();
                    global $wp_filesystem;

                    $file_path = $img_arr['path'];
                    $file_url = $img_arr['orig'];
                    
                    $filename = basename($file_url);
                    $filetype = wp_check_filetype($filename, null);
                    $file_ext = isset($filetype['ext']) ? $filetype['ext'] : '';
                    
                    if (!file_exists($file_path)) {
                        $file_path = str_replace(get_site_url() . '/', ABSPATH, $file_url);
                    }
                    
                    $portfolio_img = $file_path;
                }
            }
        }
        //
    }
    
    return $portfolio_img;
}

add_action('jobsearch_before_cand_dash_resume_contnt', 'jobsearch_cand_portfolios_move_to_encoded', 15);

function jobsearch_cand_portfolios_move_to_encoded($candidate_id) {
    $do_this_act = apply_filters('jobsearch_do_cand_portfolios_move_to_encoded', true);
    if ($do_this_act === false) {
        return false;
    }
    $protfolio_list = get_post_meta($candidate_id, 'jobsearch_field_portfolio_image', true);
    if (!empty($protfolio_list)) {
        $post_imgs_parr = get_post_meta($candidate_id, 'jobsearch_portfolio_imgs_arr', true);
        $post_imgs_parr = empty($post_imgs_parr) ? array() : $post_imgs_parr;
        
        $to_update = false;
        
        global $jobsearch_uploding_candimg, $jobsearch_download_locations;
        $jobsearch_download_locations = false;
        $jobsearch_uploding_candimg = true;
        add_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);

        $wp_upload_dir = wp_upload_dir();
        $uplod_direc_path = $wp_upload_dir['path'];
        $uplod_direc_url = $wp_upload_dir['url'];
        
        $new_portfolio_list = array();
        
        foreach ($protfolio_list as $protfolio_itm) {
            if (!is_numeric($protfolio_itm)) {
                $to_update = true;
                $attach_id = jobsearch_get_attachment_id_from_url($protfolio_itm);
                $full_image = wp_get_attachment_image_src($attach_id, 'full');
                if (isset($full_image[0]) && $full_image[0] != '') {
                    $img_path = get_attached_file($attach_id);
                    $img_url = $full_image[0];
                    $img_base_name = basename($img_url);
                    $filetype = wp_check_filetype($img_base_name, null);
                    $file_ext = isset($filetype['ext']) ? $filetype['ext'] : '';
                    
                    $port_img_name = 'port-img-' . rand(100000, 999999) . '.' . $file_ext;
                    
                    $img_new_path = $uplod_direc_path . '/' . $port_img_name;
                    @copy($img_path, $img_new_path);
                    $new_img_url = $uplod_direc_url . '/' . $port_img_name;
                    
                    $port_id = rand(100000000, 999999999);
                    
                    $post_imgs_parr[$port_id] = array(
                        'path' => $img_new_path,
                        'orig' => $new_img_url,
                    );
                    update_post_meta($candidate_id, 'jobsearch_portfolio_imgs_arr', $post_imgs_parr);
                    
                    $new_portfolio_list[] = $port_id;
                    
                    wp_delete_attachment($attach_id, true);
                }
            } else {
                $new_portfolio_list[] = $protfolio_itm;
            }
        }
        remove_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);
        //
        if ($to_update) {
            update_post_meta($candidate_id, 'jobsearch_field_portfolio_image', $new_portfolio_list);
        }
    }
}
