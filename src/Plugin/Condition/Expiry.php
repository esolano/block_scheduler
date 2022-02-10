<?php
/**
 * @file
 * Expiry condition for Blocks.
 */

namespace Drupal\block_scheduler\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides a 'Expiry' condition.
 *
 * @Condition(
 *   id = "expiry",
 *   label = @Translation("Expiry")
 * )
 */
class Expiry extends ConditionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('Expiry');
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $default_start = (!empty($this->configuration['start'])) ? DrupalDateTime::createFromTimestamp($this->configuration['start']) : '';
    $default_end = (!empty($this->configuration['end'])) ? DrupalDateTime::createFromTimestamp($this->configuration['end']) : '';

    $form['start'] = [
      '#type' => 'datetime',
      '#title' => t('Publish Date'),
      '#default_value' => $default_start,
    ];

    $form['end'] = [
      '#type' => 'datetime',
      '#title' => t('Expiry Date'),
      '#default_value' => $default_end,
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Make sure dates are filled in prior to checking
    // Make sure we are not dealing with array values
    if (!empty($form_state->getValue('start')) && !is_array($form_state->getValue('start'))
      && !empty($form_state->getValue('end')) && !is_array($form_state->getValue('end'))) {
      // Now we can validate dates since we have good data now
      if (($form_state->getValue('end')
          ->getTimestamp()) <= ($form_state->getValue('start')
          ->getTimestamp())) {
        $form_state->setErrorByName('end', $this->t('Please select expiry date greater than publish date.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    if (is_object($form_state->getValue('start'))) {
      $this->configuration['start'] = $form_state->getValue('start')
        ->getTimestamp();
    }
    else {
      $this->configuration['start'] = '';
    }

    if (is_object($form_state->getValue('end'))) {
      $this->configuration['end'] = $form_state->getValue('end')
        ->getTimestamp();
    }
    else {
      $this->configuration['end'] = '';
    }

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $status = TRUE;
    if (empty($this->configuration['start']) && empty($this->configuration['end']) && !$this->isNegated()) {
      return TRUE;
    }

    if (!empty($this->configuration['start'])) {
      $status = $status && time() >= $this->configuration['start'];
    }

    if (!empty($this->configuration['end'])) {
      $status = $status && time() <= $this->configuration['end'];
    }

    return $status;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['start' => '', 'end' => ''] + parent::defaultConfiguration();
  }

}
