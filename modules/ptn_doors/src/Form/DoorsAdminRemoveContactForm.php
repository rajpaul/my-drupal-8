<?php
namespace Drupal\ptn_doors\Form;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a confirmation form for deleting mymodule data.
 */
class DoorsAdminRemoveContactForm extends ConfirmFormBase {

  /**
   * The ID of the item to delete.
   *
   * @var string
   */
   protected $contact_type;
   protected $contact_address;
   public $PTNDoors;

   public function __construct(){
     $this->PTNDoors = new \Drupal\ptn_doors\PTNDoors();
   }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'doors_admin_notifications_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the %contact_type door contact: %contact_address?', array('%contact_type' => $this->contact_type, '%contact_address' => $this->contact_address));
  }

  /**
   * {@inheritdoc}
   */
    public function getCancelUrl() {
      return new Url('ptn_doors.doors_admin_notifications_form');
  }

  /**
   * {@inheritdoc}
   */
    public function getDescription() {
      return t('Are you sure you want to delete the %contact_type door contact: %contact_address? This cannot be undone!', array('%contact_type' => $this->contact_type, '%contact_address' => $this->contact_address));
  }

  /**
   * {@inheritdoc}
   */
    public function getConfirmText() {
    return t('Confirm');
  }

  /**
   * {@inheritdoc}
   */
    public function getCancelText() {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   *
   * @param int $id
   *   (optional) The ID of the item to be deleted.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $contact_type = NULL, $contact_address = NULL) {
    $this->contact_type = $contact_type;
    $this->contact_address = $contact_address;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // validate

    if(empty($this->contact_type) || empty($this->contact_address)) {
      drupal_set_message('Missing contact type or address', 'error');
      return;
    }

    // read list
    $contact_address = urldecode($this->contact_address);
    $contact_list = $this->PTNDoors->ptn_doors_get_notification_emails($this->contact_type);
    if(($key = array_search($this->contact_address, $contact_list)) !== false) {
      unset($contact_list[$key]);
    }

    // set new list
    $this->PTNDoors->ptn_doors_set_notification_emails($this->contact_type, $contact_list);

    drupal_set_message('Removed '.$this->contact_type.' door contact: ' . $this->contact_address);
    $response = new RedirectResponse(\Drupal::url('ptn_doors.doors_admin_notifications_form'));
    $response->send();
    exit();
  }

}

/*
 namespace Drupal\ptn_doors\Form;

 use Drupal\Core\Form\FormBase;
 use Drupal\Core\Form\FormStateInterface;

 class DoorsAdminRemoveContactForm extends FormBase {
   public $PTNDoors;

   public function __construct(){
     $this->PTNDoors = new \Drupal\ptn_doors\PTNDoors();
   }

   public function getFormId() {
     return 'doors_admin_remove_contact_form';
   }

   public function buildForm(array $form, FormStateInterface $form_state, $contact_type, $contact_address) {

      // validate
      if(empty($contact_type) || empty($contact_address)) {
        drupal_set_message('Missing contact type or address', 'error');
        return;
      }

      // read list
      $contact_address = urldecode($contact_address);
      $contact_list = $this->PTNDoors->ptn_doors_get_notification_emails($contact_type);
      if(($key = array_search($contact_address, $contact_list)) !== false) {
        unset($contact_list[$key]);
      }

      // set new list
      $this->PTNDoors->ptn_doors_set_notification_emails($contact_type, $contact_list);

      drupal_set_message('Removed contact: ' . $contact_address);
      drupal_goto('admin/ptn_doors/notifications');
   }

   public function validateForm(array &$form, FormStateInterface $form_state) {

   }

   public function submitForm(array &$form, FormStateInterface $form_state) {

   }


 }
 */
?>
