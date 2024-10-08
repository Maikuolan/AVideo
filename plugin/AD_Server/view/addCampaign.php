<?php

header('Content-Type: application/json');
require_once '../../../videos/configuration.php';
require_once $global['systemRootPath'] . 'plugin/AD_Server/Objects/VastCampaigns.php';

$obj = new stdClass();
$obj->error = true;
$obj->msg = "";

$plugin = AVideoPlugin::loadPluginIfEnabled('AD_Server');
$ad_server_location = AVideoPlugin::loadPluginIfEnabled('AD_Server_Location');

if (!User::isAdmin()) {
    $obj->msg = "You can't do this";
    die(json_encode($obj));
}

$o = new VastCampaigns(@$_POST['campId']);
$o->setName($_POST['name']);
$o->setType("Contract");
$o->setStatus($_POST['status']);
$o->setStart_date($_POST['start_date']);
$o->setEnd_date($_POST['end_date']);
$o->setPricing_model("CPM");
$o->setPriority(10);
$o->setUsers_id(User::getId());
$o->setCpm_max_prints($_POST['maxPrints']);
$o->setVisibility('listed');
$o->setReward_per_impression($_POST['reward_per_impression']);

if ($id = $o->save()) {
    $obj->error = false;
}

if (!empty($ad_server_location) && !empty($id)) {
    CampaignLocations::deleteFromCapmpaign($id);
    if (!empty($_POST['country_name'])) {
        $ad_server_location->addCampaignLocation($_POST['country_name'], $_POST['region_name'], $_POST['city_name'], $id);
    }
}

echo json_encode($obj);
