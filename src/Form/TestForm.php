<?php

namespace Drupal\test_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class TestForm extends FormBase {

    public function getFormId() {
        return 'test_form';
    }
    
    public function  buildForm(array $form, FormStateInterface $form_state) {
        $form['full_name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Your full name'),
            '#required' => 'true'
        );
        
        $form['age'] = array(
            '#type' => 'number',
            '#title' => $this->t('Your age'),
            '#required' => 'true',
            '#size' => 2
        );
        
        $form['subject'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Subject'),
            '#required' => 'true'
        );
        
        $form['message'] = array(
            '#type' => 'textarea',
            '#title' => $this->t('Message'),
            '#required' => 'true'
        );
        
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Send message'),
            '#button_type' => 'primary'
        );
        
        return $form;
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $name = $form_state->getValue('full_name');
        $age = $form_state->getValue('age');
        $subject = $form_state->getValue('subject');
        $message = $form_state->getValue('message');
        
        if (preg_match('/[^\w \']+/u', $name)) {
            $form_state->setErrorByName('full_name', $this->t('Incorrect symbols!'));
        }
        
        if (strlen($name) < 5) {
            $form_state->setErrorByName('full_name', $this->t('Too short name.'));
        }
        
        if (!preg_match('/[ ]/', $name)) {
            $form_state->setErrorByName('full_name', $this->t('Enter your full name please.'));
        }
        
        if ($age < 1) {
            $form_state->setErrorByName('age', $this->t('Age could not be a negative value or 0!'));
        }
        
        if ($age > 100) {
            $form_state->setErrorByName('age', $this->t('You are too old)))'));
        }
        
        if (preg_match('/[^\w!., \"\-\']+/u', $subject)) {
            $form_state->setErrorByName('subject', $this->t('Incorrect symbols!'));
        }
        
        if (strlen($subject) < 2) {
            $form_state->setErrorByName('message', $this->t('Too short subject.'));
        }
        
        if (strlen($message) < 5) {
            $form_state->setErrorByName('message', $this->t('Too short message.'));
        }
        
        if (strlen($message) > 1000) {
            $form_state->setErrorByName('message', $this->t('Too long message.'));
        }
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $to = \Drupal::config('system.site')->get('mail');
        $subject = $form_state->getValue('subject');
        $message = 'Name: ' . $form_state->getValue('full_name') . '<br>' .
                'Age: ' . $form_state->getValue('age') . '<br>' .
                'Message: ' . $form_state->getValue('message') . '<br>';
        $result = mail($to, $subject, $message);
        
        if ($result)
            drupal_set_message($this->t('Form successfully sent!'));
        else
            drupal_set_message($this->t('Form did not send!!!!'));
    }
    
}

