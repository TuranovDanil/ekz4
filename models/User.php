<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property string $password
 * @property int $role
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            ['login', 'unique'],
            [['role'], 'integer'],
            [['login', 'password'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'password' => 'Password',
            'role' => 'Role',
        ];
    }

    public function validatePassword($password)
    {
        return $this->password = md5($password);
    }

    static public function findByUsername($username)
    {
        return self::find()->where(['login'=>$username])->one();
    }

    public function validateLogin($attr)
    {
        $user = self::find()->where(['login'=>$this->login])->one();
        if($user !== null){
            $this->addError($attr, 'Login unique');
        }
    }

    public function beforeSave($insert)
    {
        $this->password = md5($this->password);
        return true;
    }

    public function isAdmin()
    {
        return $this->role === 1;
    }

}