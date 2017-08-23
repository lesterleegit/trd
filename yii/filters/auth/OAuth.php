<?php

namespace lesterleegit\trd\yii\filters\auth;


use yii\helpers\Url;

/**
 * Class OAuth
 * @package lesterleegit\trd\yii\filters\auth
 */
class OAuth extends AuthMethod
{
    public $auth;

    public function authenticate($user, $request, $response)
    {
        if (!\Yii::$app->user->isGuest) {
            if (Url::previous() && Url::previous() != \Yii::$app->getHomeUrl()) {
                $redirectUrl = \Yii::$app->request->hostInfo;
                $redirectUrl .= Url::previous();

                \Yii::$app->session->set('__returnUrl', '');
                \Yii::$app->response->redirect($redirectUrl);
            }
            return true;
        } else {

            if (SAND_BOX) {
                Url::remember();
                $memberModel = ClubMember::findOne([
                    'member_wxopenid' => 'obLkFwGvS6F7NLFGmaIOVSlKB0e4'
                ]);
                $identity = MemberFn::findIdentity($memberModel->member_id);

                if ($identity && \Yii::$app->user->login($identity, 5 * 60)) {
                    if (Url::previous() && Url::previous() != \Yii::$app->getHomeUrl()) {
                        $redirectUrl = \Yii::$app->request->hostInfo;
                        $redirectUrl .= Url::previous();

                        \Yii::$app->session->set('__returnUrl', '');
                        \Yii::$app->response->redirect($redirectUrl);
                        return true;

                    } else {
                        return \Yii::$app->response->redirect(Url::home());
                    }
                }
            }
            
            //登录
            Url::remember();
            return null;
        }
    }
}
