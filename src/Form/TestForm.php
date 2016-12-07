<?php

/**
 * @file
 * Contains Drupal\test_form\Form\TestForm
 */

namespace Drupal\test_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;



/**
 * Inherits by Form API base class
 * @see \Drupal\Core\Form\FormBase
 */

class TestForm extends FormBase {

    /**
     * Form name
     */

    public function getFormId() {
        return 'test_form';
    }

    /**
     * Form structure
     */
    
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
            '#size' => 2,
        );
        
        $form['subject'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Subject'),
            '#required' => 'true',
        );
        
        $form['message'] = array(
            '#type' => 'textarea',
            '#title' => $this->t('Message'),
            '#required' => 'true',
        );
        
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Send message'),
            '#button_type' => 'primary',
        );
        
        return $form;
    }

    /**
     * validate form
     */
    
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

    /**
     * Send email on submit
     */

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $to = \Drupal::config('system.site')->get('mail');
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $params = array(
            'name' => $form_state->getValue('full_name'),
            'age' => $form_state->getValue('age'),
            'message' => $form_state->getValue('message'),
            'subject' => $form_state->getValue('subject')
        );

        $message = \Drupal::service('plugin.manager.mail')->mail('test_form', 'contact', $to, $langcode, $params);  // looks not pretty, but that's from documentation example...

        if ($message['result'])
            drupal_set_message($this->t('Form successfully sent!'));
        else
            drupal_set_message($this->t('Form did not send!!!!'));
    }

    /**
     * Mail template
     * Implements hook_mail
     */

    function test_form_mail($key, &$message, $params) {
        switch ($key) {
            case 'contact':
                $message['subject'] = $params['subject'];
                $message['body'][] = 'Name: ' . $params['name'] . '\n\n' .
                    'Age: ' . $params['age'] . '\n\n' .
                    'Message: ' . $params['message'] . '\n\n';
        }
    }
}

