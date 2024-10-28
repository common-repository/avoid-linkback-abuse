<fieldset>
  <label for="<?php echo $args['self']['id']; ?>">
    <input
      type="checkbox"
      name="<?php echo $args['self']['id'] ?>"
      id="<?php echo ALA_Settings::titleToID($args['self']['title']); ?>"
      <?php checked("strict", get_option($args['self']['id'])) ?>
      value="strict"
    >
    Remove URL input field from Comments form
  </label>
</fieldset>
