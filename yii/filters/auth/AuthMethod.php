<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lesterleegit\trd\yii\filters\auth;

use GuzzleHttp\Client;
use Yii;
use yii\base\ActionFilter;
use yii\filters\auth\AuthInterface;
use yii\helpers\Url;
use yii\web\UnauthorizedHttpException;
use yii\web\User;
use yii\web\Request;
use yii\web\Response;

/**
 * AuthMethod is a base class implementing the [[AuthInterface]] interface.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class AuthMethod extends ActionFilter implements AuthInterface
{
    /**
     * @var User the user object representing the user authentication status. If not set, the `user` application component will be used.
     */
    public $user;
    /**
     * @var Request the current request. If not set, the `request` application component will be used.
     */
    public $request;
    /**
     * @var Response the response to be sent. If not set, the `response` application component will be used.
     */
    public $response;


    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $response = $this->response ?: Yii::$app->getResponse();

        $identity = $this->authenticate(
            $this->user ?: Yii::$app->getUser(),
            $this->request ?: Yii::$app->getRequest(),
            $response
        );

        if ($identity !== null) {
            return true;
        } else {
            $this->challenge($response);
            $this->handleFailure($response);
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function challenge($response)
    {
    }

    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        $headers = \Yii::$app->request->getHeaders()->toArray();
        $userAgent = $headers['user-agent'][0];
        $isWechat = preg_match("/(MicroMessenger)/is", $userAgent);

        if ($isWechat) {
            //微信
            $config = \Yii::$app->params['pay_config']['wxp']['shoppingjapan'];
            $appId = $config['appid'];
            $redirectUrl = urlencode(\Yii::$app->urlManager->getHostInfo() . Url::toRoute(['/sign-up', 'user_agent' => 1]));
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appId . '&redirect_uri=' . $redirectUrl . '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
            return $response->redirect($url);
        }


    }
}
