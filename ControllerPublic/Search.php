<?php
class AssociationMc_ControllerPublic_Search extends XenForo_ControllerPublic_Abstract {
    public function actionIndex() {
        return $this->actionView();
    }
    public static function getSessionActivityDetailsForList(array $activities) {
        return new XenForo_Phrase('mc_assoc_managing_assoc');
    }
    public function actionView() {
        $visitor = XenForo_Visitor::getInstance();
        if (!$visitor->getUserId()) {
            return $this->responseNoPermission();
        }
        $username = $this->_input->filterSingle('username', XenForo_Input::STRING);
        if (!isset($username) || strlen($username) < 1) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect(), false);
        } 
        $model = $this->getAssociationEntryModel();
        $uuid = str_replace('-', '', $username);
        if (strlen($username) <= 16) {
            $entries = $model->getEntriesByUsername($username);
            if (!is_array($entries) || empty($entries)) {
                return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect(), false);
            }
            $userId = $entries[0]['xenforo_id'];
        } elseif (strlen($uuid) === 32) {
            $entries = $model->getEntryByMinecraftUuid($uuid);
            if (!is_numeric($entries['xenforo_id'])) {
                return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect(), false);
            }
            $userId = $entries['xenforo_id'];
        } else {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect(), false);
        }
        $userModel = $this->getUserModel();
        $member = $userModel->getUserById($userId);
        return $this->responseRedirect(
            XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink('members', $member)
        );
    }
    private function getAssociationEntryModel() {
        return $this->getModelFromCache('AssociationMc_Model_AssociationEntry');
    }
    private function getUserModel() {
        return $this->getModelFromCache('XenForo_Model_User');
        }
}
