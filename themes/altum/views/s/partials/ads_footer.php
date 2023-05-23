<?php
if(
    !empty(settings()->ads->footer_stores)
    && !$this->store_user->plan_settings->no_ads
): ?>
    <div class="container my-3"><?= settings()->ads->footer_stores ?></div>
<?php endif ?>
