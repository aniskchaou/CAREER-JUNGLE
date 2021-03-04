<form method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <ul>
        <li><input type="text" name="s" placeholder="<?php esc_html_e('Keywords...', 'careerfy'); ?>"></li>
        <li><input type="submit" value="<?php esc_html_e('Search', 'careerfy'); ?>"></li>
    </ul>
</form>