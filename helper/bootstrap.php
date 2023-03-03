<?php

define("PROJECT_ROOT_PATH", __DIR__.'/../');

require_once PROJECT_ROOT_PATH.'/helper/config.php';  # configuration file

require_once PROJECT_ROOT_PATH.'/helper/function.php'; # functional file

require_once PROJECT_ROOT_PATH.'/controller/BaseController.php';  # controller(base) file

# Models
require_once PROJECT_ROOT_PATH.'/model/AsetModel.php'; # base aset model file
require_once PROJECT_ROOT_PATH.'/model/AccountModel.php'; # account model file

?>

