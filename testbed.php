<?php

include("base.class.php");
include("dpsCalc.class.php");

echo "without args = default values for Mercury LMG, should match Nutts' example\n";

$dpsObj = new dpsCalc();

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\nwith some args, testing rising CR matches nutts example%\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(0.3), 10.00, array(30), true);

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\nwith some args, testing 2cr = 51%\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(0.3), 10.00, array(30, 30));

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\nwith some args, testing 3cr = 58%\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(0.3), 10.00, array(30, 30, 30));

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\nmag size tests, Siege with mag perk and liteshow support example, should be 61\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(0.3), 10.00, array(30), false, array(2.75), 6.75, 0.00, 0.00, false, 0.00, 30, array(0.75), false, true);

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\nreload tests, should be 1.78\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(0.3), 10.00, array(30), false, array(2.75), 6.75, 0.00, 0.00, false, 0.00, 30, array(0.75), false, true, 4, array(0.75, 0.5), false);

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\nHS test, should be 14,995.08\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(), 10.00, array(), false, array(0), 6.75, 0.00, 0.00, false, 0.00, 30, array(0.75, 0.75, 0.75), false, false, 4, array(0), false, 0.25, array());

//$baseDamage = 80, $itemLevel = 50, $offence = 3522, $elemBonus = 0.2, array $damagePerks = array(0.3), $startingCritChance = 10.00, $crPerks = array(30), $hasRisingCrPerk = false, $critDamagePerks = array(2.75), $baseFireRate = 6.75, $frPerk = 0.00, $warCry = 0.00, $swan6th = false, $heroFr = 0.00, $baseMag = 30, $magPerk = 0.00, $liteshowLead = false, $liteshowSupport = false, $baseReload = 4.00, $reloadPerks = array(), $quickshot6th = false, $baseHs = 0.00, $hsPerks = array()

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\n3CD Ratatat\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(), 10.00, array(), true, array(1.35, 1.35, 1.35), 6.75, 0.00, 0.00, false, 0.00, 30, array(), false, false, 4, array(0), false, 0.25, array());

//$baseDamage = 80, $itemLevel = 50, $offence = 3522, $elemBonus = 0.2, array $damagePerks = array(0.3), $startingCritChance = 10.00, $crPerks = array(30), $hasRisingCrPerk = false, $critDamagePerks = array(2.75), $baseFireRate = 6.75, $frPerk = 0.00, $warCry = 0.00, $swan6th = false, $heroFr = 0.00, $baseMag = 30, $magPerk = 0.00, $liteshowLead = false, $liteshowSupport = false, $baseReload = 4.00, $reloadPerks = array(), $quickshot6th = false, $baseHs = 0.00, $hsPerks = array()

$dpsObj->calculate();

$dpsObj->outputResults();

echo "\n\nCR, 2CD Ratatat\n";

$dpsObj = new dpsCalc(80, 50, 3522, 0.2, array(), 10.00, array(30), true, array(1.35, 1.35), 6.75, 0.00, 0.00, false, 0.00, 30, array(), false, false, 4, array(0), false, 0.25, array());

//$baseDamage = 80, $itemLevel = 50, $offence = 3522, $elemBonus = 0.2, array $damagePerks = array(0.3), $startingCritChance = 10.00, $crPerks = array(30), $hasRisingCrPerk = false, $critDamagePerks = array(2.75), $baseFireRate = 6.75, $frPerk = 0.00, $warCry = 0.00, $swan6th = false, $heroFr = 0.00, $baseMag = 30, $magPerk = 0.00, $liteshowLead = false, $liteshowSupport = false, $baseReload = 4.00, $reloadPerks = array(), $quickshot6th = false, $baseHs = 0.00, $hsPerks = array()

$dpsObj->calculate();

$dpsObj->outputResults();

?>
