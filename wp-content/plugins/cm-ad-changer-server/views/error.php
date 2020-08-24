<div class="wrap ad_changer">
    <h2><?php echo $plugin_data['Name']; ?></h2>
    <?php
    ac_top_menu();
    if( isset($errors) && !empty($errors) )
    {
        ?>
        <ul class="ac_error cmac-clear">
            <?php
            foreach($errors as $error) echo '<li>' . $error . '</li>';
            ?>
        </ul>
        <?php
    }
    ?>
</div>