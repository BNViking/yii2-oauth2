<?php
namespace bnviking\oauth2\clients;


use bnviking\oauth2\components\OAuth2BaseClient;

/**
 * @property string $userApiUrl
 * @property string $authAppUrl
 * @property string $tokenUrl
 * @property string $userInfoUrl
 * @property string $clientID
 * @property string $clientSecret
 * @property string $redirectUrl
 * @property string $scope
 * @property string $name
 * @property string $htmlOptions
 */
final class VKontakte extends OAuth2BaseClient
{
    /** @var string Base authorization URL  */
    public string $authAppUrl = 'https://oauth.vk.com/authorize';

    /** @var string Token URL */
    public string $tokenUrl = 'https://oauth.vk.com/access_token';

    /** @var string User url API */
    public string $userApiUrl = 'https://api.vk.com/method/users.get.json';

    /** @var string Title */
    public string $name = 'VK';

    public string $versionApi = '5.81';

    public string $scope = 'email groups offline';

    public array $mapToUserResult = [
        'id' => 'id',
        'email' => 'email',
        'username' => 'screen_name',
        'token' => 'access_token',
        'tokenReset' => 'refresh_token',
        'tokenType' => 'token_type',
        'tokenExpires' => 'expires_in',
    ];

    public function updateAppAuthUrlParams(array &$params): void
    {
        $params['display'] = ['page'];
    }

    public function beforeRequestUserData(array &$config, array &$data): void
    {
        $token = $this->getToken();
        $data['v'] = $this->versionApi;
        $data['user_id'] = $token['user_id'];
        $data['fields'] = 'activities, about, blacklisted, blacklisted_by_me, books, bdate, can_be_invited_group, can_post, can_see_all_posts, can_see_audio, can_send_friend_request, can_write_private_message, career, common_count, connections, contacts, city, country, domain, education, exports, followers_count, friend_status, has_photo, has_mobile, home_town, photo_100, photo_200, photo_200_orig, photo_400_orig, photo_50, sex, site, screen_name, status, verified, games, interests, is_favorite, is_friend, is_hidden_from_feed, last_seen, maiden_name, military, movies, music, nickname, occupation, online, personal, photo_id, photo_max, photo_max_orig, quotes, relation, timezone, tv';
    }

    public function afterSetUserData(array &$user): void
    {
        $user = $user['response'][0]??[];
    }
}