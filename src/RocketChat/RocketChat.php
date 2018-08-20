<?php

namespace RocketChat;

use Httpful\Request;

class RocketChat {

    private $api;
    private $api_root = '/api/v1/';
    private $user_id;
    private $auth_token;

    function __construct($server) {
        $this->api = $server . $this->api_root;
        $tmp = Request::init()
            ->sendsJson()
            ->expectsJson();
        Request::ini($tmp);
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($val) {
        $this->user_id = $val;
    }

    public function getAuthToken() {
        return $this->auth_token;
    }

    public function setAuthToken($val) {
        $this->auth_token = $val;
    }

    public function url($method) {
        return $this->api . $method;
    }

    private function addAuthHeaders(Request &$request) {
        if (!empty($this->user_id) && !empty($this->auth_token)) {
            $request->addHeader('X-User-Id', $this->user_id);
            $request->addHeader('X-Auth-Token', $this->auth_token);
        }
    }

    public function get($method, $args = []) {
        $url = $this->url($method);
        // https://rocket.chat/docs/developer-guides/rest-api/offset-and-count-info
        if (!empty($args)) {
            $url .= '?' . join("&", $args);
        }
        $request = Request::get($url);
        $this->addAuthHeaders($request);
        $response = $request->send();
        return $response;
    }

    public function post($method, $data) {
        $request = Request::post($this->url($method));
        $this->addAuthHeaders($request);
        $response = $request->body($data)->send();
        return $response;
    }

    /**
     * Miscellaneous Information
     * https://rocket.chat/docs/developer-guides/rest-api/miscellaneous
     */

    /**
     * A simple method, requires no authentication, that returns information about the server
     * including version information.
     * https://rocket.chat/docs/developer-guides/rest-api/miscellaneous/info
     */
    public function info() {
        return $this->get('info');
    }

    /**
     * REST API Authentication
     * https://rocket.chat/docs/developer-guides/rest-api/authentication
     */

    /**
     * Login
     * https://rocket.chat/docs/developer-guides/rest-api/authentication/login
     */
    public function login($username, $password) {
        $resp = $this->post('login', [
            'username' => $username,
            'password' => $password,
        ]);
        if($resp->code == 200 && isset($resp->body->status) && $resp->body->status == 'success') {
            $this->user_id = $resp->body->data->userId;
            $this->auth_token = $resp->body->data->authToken;
        }
        return $resp;
    }

    /**
     * Logout
     * https://rocket.chat/docs/developer-guides/rest-api/authentication/logout
     */
    public function logout() {
        return $this->get('logout');
    }

    /**
     * Quick information about the authenticated user.
     * https://rocket.chat/docs/developer-guides/rest-api/authentication/me
     */
    public function me() {
        return $this->get('me');
    }

    /**
     * User Methods
     * https://rocket.chat/docs/developer-guides/rest-api/users
     */

    /**
     * Create user
     * https://rocket.chat/docs/developer-guides/rest-api/users/create
     */
    public function usersCreate($data) {
        return $this->post('users.create', $data);
    }

    /**
     * Delete user
     * https://rocket.chat/docs/developer-guides/rest-api/users/delete
     */
    public function usersDelete($user_id) {
        return $this->post('users.delete', ['userId' => $user_id]);
    }

    /**
     * Gets a user’s presence if the query string userId is provided, otherwise it gets the callee’s
     * https://rocket.chat/docs/developer-guides/rest-api/users/getpresence
     */
    public function usersGetPresence($user_id = null) {
        if (!empty($user_id)) {
            $args = ["userId=$user_id"];
        }
        return $this->get('users.getPresence', $args);
    }

    /**
     * Retrieves information about a user, the result is only limited to what the callee has access
     * to view
     * https://rocket.chat/docs/developer-guides/rest-api/users/info
     */
    public function usersInfo($user_id) {
        return $this->get('users.info', ["userId=$user_id"]);
    }

    /**
     * Gets all of the users in the system and their information, the result is only limited to what
     * the callee has access to view
     * https://rocket.chat/docs/developer-guides/rest-api/users/list
     */
    public function usersList($opt = []) {
        return $this->get('users.list', $opt);
    }

    /**
     * User set photo/avatar
     * https://rocket.chat/docs/developer-guides/rest-api/users/setavatar
     */
    public function usersSetAvatar($url) {
        return false;
    }

    /**
     * Update user
     * https://rocket.chat/docs/developer-guides/rest-api/users/update
     */
    public function usersUpdate($data) {
        return $this->post('users.update', $data);
    }

    /**
     * Channel Methods
     * https://rocket.chat/docs/developer-guides/rest-api/channels
     */

    /**
     * Adds all of the users of the Rocket.Chat server to the channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/addall
     */
    public function channelsAddAll($room_id) {
        return $this->post('channels.addAll', ['roomId' => $room_id]);
    }

    /**
     * Gives the role of moderator for a user in the currrent channel
     * https://rocket.chat/docs/developer-guides/rest-api/channels/addmoderator
     */
    public function channelsAddModerator($room_id, $user_id) {
        return $this->post('channels.addModerator', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Gives the role of owner for a user in the currrent channel
     * https://rocket.chat/docs/developer-guides/rest-api/channels/addowner
     */
    public function channelsAddOwner($room_id, $user_id) {
        return $this->post('channels.addOwner', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Archives a channel
     * https://rocket.chat/docs/developer-guides/rest-api/channels/archive
     */
    public function channelsArchive($room_id) {
        return $this->post('channels.archive', ['roomId' => $room_id]);
    }

    /**
     * Cleans up a channel, removing messages from the provided time range
     * https://rocket.chat/docs/developer-guides/rest-api/channels/cleanhistory
     */
    public function channelsCleanHistory($data) {
        return $this->post('channels.cleanHistory', $data);
    }

    /**
     * Removes the channel from the user’s list of channels
     * https://rocket.chat/docs/developer-guides/rest-api/channels/close
     */
    public function channelsClose($room_id) {
        return $this->post('channels.close', ['roomId' => $room_id]);
    }

    /**
     * Creates a new public channel, optionally including users
     * https://rocket.chat/docs/developer-guides/rest-api/channels/create
     */
    public function channelsCreate($data) {
        return $this->post('channels.create', $data);
    }

    /**
     * Retrieves the integrations which the channel has, requires the permission manage-integrations
     * and supports the Offset and Count Query Parameters.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/getintegrations
     */
    public function channelsGetIntegrations($room_id) {
        return $this->get('channels.getIntegrations', ["roomId=$room_id"]);
    }

    /**
     * Retrieves the messages from a channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/history
     */
    public function channelHistory($room_id, $opt = []) {
        return $this->get('channels.history', ["roomId=$room_id"] + $opt);
    }

    /**
     * Retrieves the information about the channel
     * https://rocket.chat/docs/developer-guides/rest-api/channels/info
     */
    public function channelsInfo($room_id) {
        return $this->get("channels.info", ["roomId=$room_id"]);
    }

    /**
     * Adds a user to the channel
     * https://rocket.chat/docs/developer-guides/rest-api/channels/invite
     */
    public function channelInvite($room_id, $user_id) {
        return $this->post('channels.invite', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Removes a user from the channel
     * https://rocket.chat/docs/developer-guides/rest-api/channels/kick
     */
    public function channelsKick($room_id, $user_id) {
        return $this->post('channels.kick', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Causes the callee to be removed from the channel
     * https://rocket.chat/docs/developer-guides/rest-api/channels/leave
     */
    public function channelsLeave($room_id) {
        return $this->post('channels.leave', ['roomId' => $room_id]);
    }

    /**
     * Lists all of the channels the calling user has joined, this method supports the Offset and
     * Count Query Parameters.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/list-joined
     */
    public function channelsListJoined($args = []) {
        return $this->get('channels.list.joined', $args);
    }

    /**
     * Lists all of the channels on the server, this method supports the Offset and Count Query
     * Parameters.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/list
     */
    public function channelsList($args = []) {
        return $this->get('channels.list', $args);
    }

    /**
     * Adds the channel back to the user’s list of channels.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/open
     */
    public function channelsOpen($room_id) {
        return $this->post('channels.open', ['roomId' => $room_id]);
    }

    /**
     * Removes the role of moderator from a user in the currrent channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/removemoderator
     */
    public function channelsRemoveModerator($room_id, $user_id) {
        return $this->post('channels.removeModerator', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Removes the role of owner from a user in the currrent channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/removeowner
     */
    public function channelsRemoveOwner($room_id, $user_id) {
        return $this->post('channels.removeOwner', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Changes the name of the channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/rename
     */
    public function channelsRename($room_id, $newname) {
        return $this->post('channels.rename', [
            'roomId' => $room_id,
            'name'   => $newname
        ]);
    }

    /**
     * Sets the description for the channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/setdescription
     */
    public function channelsSetDescription($room_id, $description) {
        return $this->post('channels.setDescription', [
            'roomId'      => $room_id,
            'description' => $description
        ]);
    }

    /**
     * Sets the code required to join the channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/setjoincode
     */
    public function channelsSetJoinCode($room_id, $code) {
        return $this->post('channels.setJoinCode', [
            'roomId'   => $room_id,
            'joinCode' => $code
        ]);
    }

    /**
     * Sets the purpose for the channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/setpurpose
     */
    public function channelsSetPurpose($room_id, $purpose) {
        return $this->post('channels.setPurpose', [
            'roomId'  => $room_id,
            'purpose' => $purpose
        ]);
    }

    /**
     * Sets whether the channel is read only or not.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/setreadonly
     */
    public function channelsSetReadOnly($room_id, $ro) {
        return $this->post('channels.setReadOnly', [
            'roomId'   => $room_id,
            'readOnly' => $ro
        ]);
    }

    /**
     * Sets the topic for the channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/settopic
     */
    public function channelsSetTopic($room_id, $topic) {
        return $this->post('channels.setTopic', [
            'roomId' => $room_id,
            'topic'  => $topic
        ]);
    }

    /**
     * Sets the type of room this channel should be
     * https://rocket.chat/docs/developer-guides/rest-api/channels/settype
     */
    public function channelsSetType($room_id, $type) {
        return $this->post('channels.setType', [
            'roomId' => $room_id,
            'topic'  => $type
        ]);
    }

    /**
     * Unarchives a channel.
     * https://rocket.chat/docs/developer-guides/rest-api/channels/unarchive
     */
    public function channelsUnarchive($room_id) {
        return $this->post('channels.unarchive', ['roomId' => $room_id]);
    }

    /**
     * Group Methods
     * https://rocket.chat/docs/developer-guides/rest-api/groups
     */

    /**
     * Gives the role of moderator for a user in the currrent group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/addmoderator
     */
    public function groupsAddModerator($room_id, $user_id) {
        return $this->post('groups.addModerator', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Gives the role of owner for a user in the currrent group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/addowner
     */
    public function groupsAddOwner($room_id, $user_id) {
        return $this->post('groups.addOwner', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Gives the role of leader for a user in the currrent group.
     */
    public function groupsAddLeader($room_id, $user_id) {
        return $this->post('groups.addLeader', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Archives a private group, only if you’re part of the group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/archive
     */
    public function groupsArchive($room_id) {
        return $this->post('groups.archive', ['roomId' => $room_id]);
    }

    /**
     * Creates a new private group, optionally including users, only if you’re part of the group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/create
     */
    public function groupsCreate($data) {
        return $this->post('groups.create', $data);
    }

    /**
     * Removes the private group from the user’s list of groups, only if you’re part of the group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/close
     */
    public function groupsClose($room_id) {
        return $this->post('groups.close', ['roomId' => $room_id]);
    }

    /**
     * Retrieves the integrations which the group has, requires the permission manage-integrations
     * and supports the Offset and Count Query Parameters.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/getintegrations
     */
    public function groupsGetIntegrations($room_id, $opt = []) {
        return $this->get('groups.getIntegrations', ["roomId=$room_id"] + $opt);
    }

    /**
     * Retrieves the messages from a private group, only if you’re part of the group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/history
     */
    public function groupsHistory($room_id, $opt = []) {
        return $this->get('groups.history', ["roomId=$room_id"] + $opt);
    }

    /**
     * Retrieves the information about the private group, only if you’re part of the group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/info
     */
    public function groupsInfo($room_id = null, $room_name = null) {
        if (!empty($room_id)) {
            return $this->get('groups.info', ["roomId=$room_id"]);
        }
        if (!empty($room_name)) {
            $room_name = urlencode($room_name);
            return $this->get('groups.info', ["roomName=$room_name"]);
        }
    }

    /**
     * Adds a user to the private group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/invite
     */
    public function groupsInvite($room_id, $user_id) {
        return $this->post('groups.invite', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Removes a user from the private group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/kick
     */
    public function groupsKick($room_id, $user_id) {
        return $this->post('groups.kick', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Causes the callee to be removed from the private group, if they’re part of it and are not the
     * last owner.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/leave
     */
    public function groupsLeave($room_id) {
        return $this->post('groups.leave', ['roomId' => $room_id]);
    }

    /**
     * Gives the role of moderator for a user in the currrent group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/addmoderator
     */
    public function groupsList($opt = []) {
        return $this->get('groups.list', $opt);
    }

    /**
     * Adds the private group back to the user’s list of private groups.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/open
     */
    public function groupsOpen($room_id) {
        return $this->post('groups.open', ['roomId' => $room_id]);
    }

    /**
     * Removes the role of moderator from a user in the currrent group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/removemoderator
     */
    public function groupsRemoveModerator($room_id, $user_id) {
        return $this->post('groups.removeModerator', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Removes the role of owner from a user in the currrent Group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/removeowner
     */
    public function groupsRemoveOwner($room_id, $user_id) {
        return $this->post('groups.removeOwner', [
            'roomId' => $room_id,
            'userId' => $user_id
        ]);
    }

    /**
     * Changes the name of the private group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/rename
     */
    public function groupsRename($room_id, $newname) {
        return $this->post('groups.rename', [
            'roomId' => $room_id,
            'name'   => $newname
        ]);
    }

    /**
     * Sets the description for the private group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/setdescription
     */
    public function groupsSetDescription($room_id, $description) {
        return $this->post('groups.setDescription', [
            'roomId'      => $room_id,
            'description' => $description
        ]);
    }

    /**
     * Sets the purpose for the private group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/setpurpose
     */
    public function groupsSetPurpose($room_id, $purpose) {
        return $this->post('groups.setPurpose', [
            'roomId'  => $room_id,
            'purpose' => $purpose
        ]);
    }

    /**
     * Sets whether the group is read only or not.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/setreadonly
     */
    public function groupsSetReadOnly($room_id, $ro) {
        return $this->post('groups.setReadOnly', [
            'roomId'   => $room_id,
            'readOnly' => $ro
        ]);
    }

    /**
     * Sets the topic for the private group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/settopic
     */
    public function groupsSetTopic($room_id, $topic) {
        return $this->post('groups.setTopic', [
            'roomId' => $room_id,
            'topic'  => $topic
        ]);
    }

    /**
     * Sets the type of room this group should be.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/settype
     */
    public function groupsSetType($room_id, $type) {
        return $this->post('groups.addModerator', [
            'roomId' => $room_id,
            'type'   => $type
        ]);
    }

    /**
     * Unarchives a private group.
     * https://rocket.chat/docs/developer-guides/rest-api/groups/unarchive
     */
    public function groupsUnarchive($room_id) {
        return $this->post('groups.unarchive', ['roomId' => $room_id]);
    }

    /**
     * IM Methods
     * https://rocket.chat/docs/developer-guides/rest-api/im
     */

    /**
     * Removes the direct message from the user’s list of direct messages.
     * https://rocket.chat/docs/developer-guides/rest-api/im/close
     */
    public function imClose($room_id) {
        return $this->post('im.close', ['roomId' => $room_id]);
    }

    /**
     * Retrieves the messages from a direct message.
     * https://rocket.chat/docs/developer-guides/rest-api/im/history
     */
    public function imHistory($room_id, $opt = []) {
        return $this->get('im.history', ["roomId=$room_id"] + $opt);
    }

    /**
     * Lists all of the direct messages in the server, requires the permission
     * view-room-administration permission and this method supports the Offset and Count Query
     * Parameters.
     * https://rocket.chat/docs/developer-guides/rest-api/im/list-everyone
     */
    public function imListEveryone($opt = []) {
        return $this->get('im.list.everyone', $opt);
    }

    /**
     * Lists all of the direct messages the calling user has joined, this method supports the Offset
     * and Count Query Parameters.
     * https://rocket.chat/docs/developer-guides/rest-api/im/list
     */
    public function imList($opt = []) {
        return $this->get('im.list', $opt);
    }

    /**
     * Retrieves the messages from any direct message in the server, this method supports the Offset
     * and Count Query Parameters.
     *
     * For this method to work the Enable Direct Message History Endpoint setting must be true, and
     * the user calling this method must have the view-room-administration permission.
     * https://rocket.chat/docs/developer-guides/rest-api/im/close
     */
    public function imMessagesOthers($room_id, $opt = []) {
        return $this->get('im.messages.others', ["roomId=$room_id"] + $opt);
    }

    /**
     * Adds the direct message back to the user’s list of direct messages.
     * https://rocket.chat/docs/developer-guides/rest-api/im/open
     */
    public function imOpen($room_id) {
        return $this->post('im.open', ['roomId' => $room_id]);
    }

    /**
     * Sets the topic for the direct message.
     * https://rocket.chat/docs/developer-guides/rest-api/im/settopic
     */
    public function imSetTopic($room_id, $topic) {
        return $this->post('im.setTopic', [
            'roomId' => $room_id,
            'topic'  => $topic
        ]);
    }

    /**
     * Chat Methods
     * https://rocket.chat/docs/developer-guides/rest-api/chat
     */

    /**
     * Chat message delete
     * https://rocket.chat/docs/developer-guides/rest-api/chat/delete
     */
    public function chatDelete($room_id, $msg_id, $as_user = false) {
        return $this->post('chat.delete', [
            'roomId' => $room_id,
            'msgId'  => $msg_id,
            'asUser' => $as_user
        ]);
    }

    /**
     * Post a chat message
     * https://rocket.chat/docs/developer-guides/rest-api/chat/postmessage
     */
    public function chatPostMessage($data) {
        return $this->post('chat.postMessage', $data);
    }

    /**
     * Pins a Chat Message
     * https://rocket.chat/docs/developer-guides/rest-api/chat/pinmessage
     */
    public function chatPinMessage($message_id) {
        return $this->post('chat.pinMessage', ['messageId' => $message_id]);
    }

    /**
     * Chat message update
     * https://rocket.chat/docs/developer-guides/rest-api/chat/update
     */
    public function chatUpdate($room_id, $msg_id, $text) {
        return $this->post('chat.update', [
            'roomId' => $room_id,
            'msgId'  => $msg_id,
            'text'   => $text
        ]);
    }

    /**
     * Settings Methods
     * https://rocket.chat/docs/developer-guides/rest-api/settings
     */

    /**
     * Gets the setting for the provided _id.
     * https://rocket.chat/docs/developer-guides/rest-api/settings/get
     */
    public function settingsGet($id) {
        return $this->get("settings/$id");
    }

    /**
     * Updates the setting for the provided _id.
     * https://rocket.chat/docs/developer-guides/rest-api/settings/update
     */
    public function settingsUpdate($id, $data) {
        return $this->post("settings/$id", $data);
    }

    /**
     * Integrations
     * https://rocket.chat/docs/developer-guides/rest-api/integration
     */

    /**
     * Creates an integration, if the callee has the permission.
     * https://rocket.chat/docs/developer-guides/rest-api/integration/create
     */
    public function integrationsCreate($data) {
        return $this->post('integrations.create', $data);
    }

    /**
     * Lists all of the integrations on the server, this method supports the Offset and Count Query
     * Parameters.
     * https://rocket.chat/docs/developer-guides/rest-api/integration/list
     */
    public function integrationsList($opt = []) {
        return $this->get('integrations.list', $opt);
    }

    /**
     * Removes an integration from the server.
     * https://rocket.chat/docs/developer-guides/rest-api/integration/remove
     */
    public function integrationsRemove($type, $id) {
        return $this->post('integrations.remove', [
            'type'           => $type,
            'integrationId'  => $id
        ]);
    }

    /**
     * Livechat
     * https://rocket.chat/docs/developer-guides/rest-api/livechat
     */

    /**
     * ...
     */

}
