<?php

/**
 * class to implement NuttsNBolts DPS calculations
 * see https://seebot.dev/dps_calc_tutorial.php for methodology
 */
class dpsCalc extends base
{

    /**
     * vars which we input
     */
    public $baseDamage = 80.00;
    public $itemLevel = 50;
    public $offence = 3522.00;
    public $elemBonus = 0.2;
    public $damagePerks = array(0.3);
    public $startingCritChance = 10.0;
    public $crPerks = array(30);
    public $hasRisingCrPerk = false;
    public $critDamagePerks = array(2.75);
    public $baseFireRate = 6.75;
    public $frPerk = 0.00;
    public $warCry = 0.00;
    public $swan6th = 0.00;
    public $heroFr = 0.00;
    public $baseMag = 30;
    public $baseReload = 4.00;
    public $baseHs = 0.00;
    public $hsPerks = array();
    public $accuracyEstimate = 0.5;

    /**
     * vars which are calculated
     */
    public $weaponDamageValue = 0.00;
    public $weaponTrueDamage = 0.00;
    public $critChance = 0.00;
    public $critDamage = 0.00;
    public $critModifier = 0.00;
    public $fireRate = 0.00;
    public $trueDps = 0.00;

    public $magPerks = array();
    public $magSize = 0.00;
    public $reloadPerks = array();
    public $reload = 0.00;

    public $effectiveFiringTime = 0.00;
    public $dpsInclReloads = 0.00;
    public $dpsInclChaosAgentReloads = 0.00;
    public $headshotDamage = 0.00;
    public $headshotDamageDiff = 0.00;

    public function __construct($baseDamage = 80, $itemLevel = 50, $offence = 3522, $elemBonus = 0.2, array $damagePerks = array(0.3), $startingCritChance = 10.00, $crPerks = array(30), $hasRisingCrPerk = false, $critDamagePerks = array(2.75), $baseFireRate = 6.75, $frPerk = 0.00, $warCry = 0.00, $swan6th = false, $heroFr = 0.00, $baseMag = 30, $magPerks = array(), $liteshowLead = false, $liteshowSupport = false, $baseReload = 4.00, $reloadPerks = array(), $quickshot6th = false, $baseHs = 0.00, $hsPerks = array(), $accuracyEstimate = 0.5)
    {

        $this->baseDamage = floatval($baseDamage);
        $this->itemLevel = intval($itemLevel);
        $this->offence = floatval($offence);
        $this->elemBonus = floatval($elemBonus);
        $this->damagePerks = $this->makeFloatArraySafe($damagePerks);
        $this->startingCritChance = floatval($startingCritChance);
        $this->crPerks = $this->makeFloatArraySafe($crPerks);
        $this->critDamagePerks = $this->makeFloatArraySafe($critDamagePerks);

        if ($hasRisingCrPerk) {

            $this->hasRisingCrPerk = true;

        }

        $this->baseFireRate = floatval($baseFireRate);
        $this->frPerk = floatval($frPerk);
        $this->warCry = floatval($warCry);

        if ($swan6th) {

            $this->swan6th = 0.39;

        }

        $this->heroFr = floatval($heroFr);

        $this->baseMag = $baseMag;

        $this->magPerks = $this->makeFloatArraySafe($magPerks);

        if ($liteshowLead) {

            $this->magPerks[] = 0.9;

        } else {

            if ($liteshowSupport) {

                $this->magPerks[] = 0.3;

            }

        }

        $this->baseReload = floatval($baseReload);
        $this->reloadPerks = $this->makeFloatArraySafe($reloadPerks);

        if ($quickshot6th) {

            $this->reloadPerks[] = 0.5;

        }

        $this->baseHs = floatval($baseHs);
        $this->hsPerks = $this->makeFloatArraySafe($hsPerks);

        $this->accuracyEstimate = floatval($accuracyEstimate);

        //todo - add in some validation here, ie, accuracy cannot be > 1

    }

    public function calculate()
    {

        $this->stage1_step1_calcTrueDps();
        $this->stage1_step2_damagePerks();
        $this->stage1_step3_critRatingConversion();
        $this->stage1_step4_calculateCritModifier();
        $this->stage1_step5_calculateFireRate();
        $this->stage1_step6_applyCritModifierAndFireRate();

        $this->stage2_step1_calculateMagSize();
        $this->stage2_step2_calculateReloadTime();
        $this->stage2_step3_calculateEffectiveFiringTime();
        $this->stage2_step4_calculateDpsInclReloads();
        $this->stage2_step4a_estimateDpsInclChaosAgentReloads();
        $this->stage2_step5_calculateHeadshotDamage();
        $this->stage2_step6_applyHeadshotToDps();

    }

    public function stage1_step1_calcTrueDps()
    {

        $this->weaponDamageValue = $this->baseDamage * (1 + ($this->itemLevel - 1) * 0.05) * (1 + $this->offence / 100);

    }

    public function stage1_step2_damagePerks()
    {

        $multiplier = 1 + $this->elemBonus;

        if ($this->canLoop($this->damagePerks)) {

            foreach ($this->damagePerks as $dmgPerk) {

                $multiplier += $dmgPerk;

            }

        }

        $this->weaponTrueDamage = $this->weaponDamageValue * $multiplier;

    }

    public function stage1_step3_critRatingConversion()
    {

        $this->critChance = $this->startingCritChance;

        $cr = 0.00;

        if ($this->canLoop($this->crPerks)) {

            foreach ($this->crPerks as $crPerk) {

                $cr += $crPerk;

            }

        }

        //rising CR perk?
        if ($this->hasRisingCrPerk) {

            $cr += (1.8 * 15);

        }

        $addedCc = ((75 * $cr) / (50 + $cr));

        $this->critChance += $addedCc;

        $this->critChance = $this->roundDownToNearestPointFive($this->critChance);

    }

    public function stage1_step4_calculateCritModifier()
    {

        $this->critDamage = 0.00;

        if ($this->canLoop($this->critDamagePerks)) {

            foreach ($this->critDamagePerks as $cd) {

                $this->critDamage += $cd;

            }

        }

        $this->critModifier = 1 + ($this->critChance / 100) * $this->critDamage;

    }

    public function stage1_step5_calculateFireRate()
    {

        $this->fireRate = $this->baseFireRate * (1 + $this->frPerk + $this->warCry + $this->swan6th + $this->heroFr);

    }

    public function stage1_step6_applyCritModifierAndFireRate()
    {

        $this->trueDps = $this->weaponTrueDamage * $this->critModifier * $this->fireRate;

    }

    public function stage2_step1_calculateMagSize()
    {

        $multiplier = 1;

        if ($this->canLoop($this->magPerks)) {

            foreach ($this->magPerks as $magPerk) {

                $multiplier += $magPerk;

            }

        }

        $this->magSize = intval($this->baseMag * $multiplier);

    }

    public function stage2_step2_calculateReloadTime()
    {

        $divisor = 1;

        if ($this->canLoop($this->reloadPerks)) {

            foreach ($this->reloadPerks as $reloadPerk) {

                $divisor += $reloadPerk;

            }

        }

        $this->reload = round($this->baseReload / $divisor, 2);

    }

    public function stage2_step3_calculateEffectiveFiringTime()
    {

        $this->effectiveFiringTime = ($this->magSize / $this->fireRate) / ($this->magSize / $this->fireRate + $this->reload);

    }

    public function stage2_step4_calculateDpsInclReloads()
    {

        /*WeaponTrueDamage * CritModifier * FireRate * EffectiveFiringTime
= TrueDPS[Reloads Inclusive]*/
        $this->dpsInclReloads = $this->weaponTrueDamage * $this->critModifier * $this->fireRate * $this->effectiveFiringTime;

    }

    public function stage2_step4a_estimateDpsInclChaosAgentReloads()
    {

        //arbitrary estimate of 95% uptime for Chaos Agent reloads
        $this->dpsInclChaosAgentReloads = $this->weaponTrueDamage * $this->critModifier * $this->fireRate * 0.95;

    }

    public function stage2_step5_calculateHeadshotDamage()
    {

        //WeaponTrueDamage * (1 + (BaseHS * (1 + HSPerk))

        $multiplier = 1;

        if ($this->canLoop($this->hsPerks)) {

            foreach ($this->hsPerks as $hsPerk) {

                $multiplier += $hsPerk;

            }

        }

        $this->headshotDamage = $this->weaponTrueDamage * (1 + ($this->baseHs * $multiplier));

    }

    public function stage2_step6_applyHeadshotToDps()
    {

        $this->headshotDamageDiff = $this->headshotDamage - $this->weaponTrueDamage;

        $this->dpsInclHeadshots = ($this->weaponTrueDamage * $this->critModifier * $this->fireRate) + ($this->headshotDamageDiff * $this->fireRate * $this->accuracyEstimate);

    }

    public function outputResults()
    {

        echo "Base damage (weaponDamageValue): " . $this->safe_number_format($this->weaponDamageValue) . "\n";
        echo "Damage after dmg perks (weaponTrueDamage): " . $this->safe_number_format($this->weaponTrueDamage) . "\n";
        echo "Crit chance (critChance): " . $this->critChance . "\n";
        echo "Crit damage (critDamage): " . $this->critDamage . "\n";
        echo "Crit modifier (critModifier): " . $this->critModifier . "\n";
        echo "Fire rate (fireRate): " . $this->fireRate . "\n";
        echo "True DPS (trueDps): " . $this->safe_number_format($this->trueDps) . "\n";
        echo "Mag Size (magSize): " . $this->magSize . "\n";
        echo "Reload time (reload): " . $this->reload . "\n";
        echo "Effective firing time (effectiveFiringTime): " . $this->effectiveFiringTime . "\n";
        echo "DPS incl reloads (dpsInclReloads): " . $this->safe_number_format($this->dpsInclReloads) . "\n";
        echo "DPS incl Chaos Agent reloads - estimated 95% uptime (dpsInclChaosAgentReloads): " . $this->safe_number_format($this->dpsInclChaosAgentReloads) . "\n";
        echo "Headshot damage (headshotDamage): " . $this->safe_number_format($this->headshotDamage) . "\n";
        echo "Headshot damage difference (headshotDamageDiff): " . $this->safe_number_format($this->headshotDamageDiff) . "\n";
        echo "DPS incl headshots (dpsInclHeadshots): " . $this->safe_number_format($this->dpsInclHeadshots) . "\n";

    }

}

?>
