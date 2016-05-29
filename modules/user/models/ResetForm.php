<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use app\modules\user\models\User;
use app\modules\user\Module;


/**
 * @property User $_user
 */
class ResetForm extends Model
{
    private $_user      = false;
    public $username_or_email;
    public $reCaptcha;
    public $email;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username_or_email'], 'required'],            
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => Yii::$app->reCaptcha->secret],
            [['username_or_email'],'validateStatus'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'email'         =>  Module::t('user','reset.attr.username_or_email'),
        ];
    }    

    
    public function validateStatus($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || $user->status != User::STATUS_ACTIVE) {
                $this->addError($attribute, Module::t('user', 'reset.vld.error'));
            }
        }
    }

    /**
     * Finds user by [[email,username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if (!$this->_user){
            if (!filter_var($this->username_or_email,FILTER_VALIDATE_EMAIL)){
                $this->_user    =   User::findIdentityByUsername($this->username_or_email);
            } else {
                $this->_user = User::findIdentityByEmail($this->username_or_email);
                if ($this->_user === NULL) {
                    $this->_user    =   User::findIdentityByUsername($this->username_or_email);
                }
            }            
        }
        return $this->_user;
    }

    public function afterValidate()
    {
        parent::afterValidate(); // TODO: Change the autogenerated stub
        $this->email    =   $this->getUser()->email;
        return true;
    }
}
