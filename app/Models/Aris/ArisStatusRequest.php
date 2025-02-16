<?php

namespace App\Models\Aris;

use GuzzleHttp\Client;
use App\Models\Esims\Esim;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ArisStatusRequest
 *
 * @package App\Models\Aris
 * @property integer $id
 *
 * @property int|null $min_esim_id
 * @property int $last_requested_esim_id
 * @property int|null $max_esim_id
 * @property int $prev_request_status
 * @property int $request_status
 * @property int $last_response_code
 * @property Carbon|null $start_at
 * @property Carbon|null $end_at
 * @property string|null $request_message
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Carbon|null $last_queueing_start_at
 * @property Carbon|null $last_queueing_end_at
 * @property int|null $last_queueing_job_id
 *
 * @property int|null $requests_to_send_count
 * @property int|null $requests_sent_count
 * @property int|null $requests_sent_rate
 *
 * @property int|null $requests_waiting_result_count
 * @property int|null $requests_waiting_result_rate
 *
 * @property int|null $requests_received_count
 * @property int|null $requests_received_rate
 */
class ArisStatusRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static int $STATUS_CODE_WAITING = 2;
    public static int $STATUS_CODE_BUSY = 3;
    public static int $STATUS_CODE_QUEUEING = 4;
    public static string $SUCCESS_MESSAGE = "Success";
    public static int $STATUS_CODE_SUCCESS = 1;
    public static int $STATUS_CODE_FAIL = -1;

    #region Validation Rules

    public static function defaultRules() {
        return [
        ];
    }
    public static function createRules() {
        return array_merge(self::defaultRules(), [
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
        ]);
    }
    public static function messagesRules() {
        return [
        ];
    }

    #endregion

    #region Custom Functions
    public static function startNew(): ?ArisStatusRequest
    {
        /*if ( ArisStatusRequest::whereRequestStatus( self::$STATUS_CODE_WAITING )->count() > 0 ) {
            return null;
        }*/
        if ( ArisStatusRequestStat::isThereRequestsToBeProcessed() ) {
            return null;
        }
        /*if ( ArisStatusRequest::whereRequestStatus( self::$STATUS_CODE_WAITING )->count() === self::getMaxRunningRequests() ) {
            return null;
        }*/
        if ( ArisStatusRequestStat::isMaxRequestsToBeProcessedReached() ) {
            return null;
        }

        if ( ArisStatusRequestStat::isMaxWaitingRequestsReached() ) {
            return null;
        }

        $last_request = ArisStatusRequest::orderBy('id', 'desc')->first();
        $arisstatusrequest = New ArisStatusRequest();

        $arisstatusrequest->setStarted();
        $arisstatusrequest->setNextEsimsInterval($last_request);

        $arisstatusrequest->setRequestsToSendCount();
        ArisStatusRequestStat::incrementRequestsToSendCount( $arisstatusrequest->requests_to_send_count );

        return $arisstatusrequest;
    }

    #region Manage Status
    private function setPrevStatus(bool $save = true) {
        $this->prev_request_status = is_null( $this->request_status ) ? 0 : $this->request_status;
        if ( $save ) {
            $this->save();
        }
    }
    private function setStatus($newvalue, bool $save = true) {
        Log::error("ArisStatusRequest - Change Status - PREV: " . $this->prev_request_status . ". From ( " . $this->request_status . " ) To ( " . $newvalue . " )");
        $this->setPrevStatus(false);
        $this->request_status = $newvalue;
        if ( $save ) {
            $this->save();
        }
    }
    public function setStarted() {
        $this->start_at = Carbon::now();
        $this->save();

        if ( $this->request_status !== self::$STATUS_CODE_WAITING ) {
            $this->setStatus(self::$STATUS_CODE_WAITING);
            ArisStatusRequestStat::incrementWaitingRequestsCount();
        }
    }
    public function setWaiting() {
        if ( $this->request_status !== self::$STATUS_CODE_WAITING ) {
            $this->setStatus(self::$STATUS_CODE_WAITING);

            ArisStatusRequestStat::incrementWaitingRequestsCount();
            $this->decrementStatPreviousStatusRequests();
        }
    }
    public function setBusy() {
        if ( $this->request_status !== self::$STATUS_CODE_BUSY ) {
            $this->setStatus(self::$STATUS_CODE_BUSY);

            ArisStatusRequestStat::incrementRequestsBusyCount();
            $this->decrementStatPreviousStatusRequests();
        }
    }
    public function setQueueing(string $job_id = null) {
        if ( $this->request_status !== self::$STATUS_CODE_QUEUEING ) {
            $this->setStatus(self::$STATUS_CODE_QUEUEING, false);

            $this->last_queueing_start_at = Carbon::now();
            if (!is_null($job_id)) {
                $this->last_queueing_job_id = $job_id;
            }
            $this->save();

            ArisStatusRequestStat::incrementRequestsQueueingCount();
            $this->decrementStatPreviousStatusRequests();
        }
    }
    public function endQueueing() {
        $this->last_queueing_end_at = Carbon::now();
        /*if ( $this->request_status === self::$STATUS_CODE_QUEUEING ) {
            $this->setPrevStatus(false);
            $this->decrementStatPreviousStatusRequests();
        }*/
        $this->save();
    }
    public function setSuccess(bool $save = true) {
        if ( $this->request_status !== self::$STATUS_CODE_SUCCESS) {
            $this->setStatus(self::$STATUS_CODE_SUCCESS);
            $this->decrementStatPreviousStatusRequests();
        }
    }
    public function setFail(bool $save = true) {
        if ( $this->request_status !== self::$STATUS_CODE_FAIL ) {
            $this->setStatus(self::$STATUS_CODE_FAIL);
            $this->decrementStatPreviousStatusRequests();
        }
    }
    #endregion

    public function setNextEsimsInterval(ArisStatusRequest $last_request = null) {

        if ( is_null($last_request) ) {
            $min_esim_id = 1;
        } else {
            // get max ID
            $max_esim_id = Esim::max("id");
            if ($max_esim_id <= $last_request->last_requested_esim_id) {
                $min_esim_id = 1;
            } else {
                $min_esim = Esim::where('id', '>', $last_request->last_requested_esim_id)->orderBy('id', 'asc')->first();
                if ($min_esim) {
                    $min_esim_id = $min_esim->id;
                } else {
                    $min_esim_id = 1;
                }
            }
        }

        $this->min_esim_id = $min_esim_id;
        $this->max_esim_id = Esim::where('id', '>=', $min_esim_id + ArisStatusRequest::getMaxEsimsByRequest())->orderBy('id', 'asc')->first()->id;

        $this->save();
    }

    public function execAll() {
        while ( $this->last_requested_esim_id !== $this->max_esim_id ) {
            $this->execNextEsim();
        }
    }

    public function execNextEsim() {

        $this->setBusy();

        if ( ArisStatusRequestStat::isMaxWaitingRequestsReached() ) {
            $this->setWaiting();
        } else {
            $next_esim = Esim::whereBetween('id', [$this->last_requested_esim_id + 1, $this->max_esim_id])->orderBy('id', 'asc')->first();

            if ($next_esim->id < $this->last_requested_esim_id) {
                $next_esim = Esim::find($this->last_requested_esim_id);
            }

            $exec_result = self::execEsim($next_esim, $this->id);

            if ($exec_result['message'] === self::$SUCCESS_MESSAGE) {
                $this->last_requested_esim_id = $next_esim->id;

                if ($this->last_requested_esim_id === $this->max_esim_id) {
                    $this->end_at = Carbon::now();
                    $this->setSuccess();
                } else {
                    $this->setWaiting();
                }

                $this->incrementRequestsSentCount(1, false);
            } else {
                $this->setFail();
            }

            $this->last_response_code = $exec_result['response_code'];
            $this->request_message = $exec_result['message'];
        }

        $this->save();
    }

    public static function execEsim(Esim $esim, $request_id)
    {
        $result_message = self::$SUCCESS_MESSAGE;
        $response_url = "http://gtesimtest.moov-africa.ga/api/arisstatuses";
        $response_code = 0;
        try {
            //Create Client object to deal with
            $client = new Client();

            // Define the request parameters
            $post_link = "http://192.168.5.113:8080/restapi/imsistatus.php";

            $headers = [
                'Content-Type' => 'application/json',
            ];

            $data = [
                'iccid' => $esim->iccid,
                'imsi' => $esim->imsi,
                'request_id' => $request_id,
                'response_url' => $response_url,
            ];

            // POST request using the created object
            $postResponse = $client->post($post_link, [
                'headers' => $headers,
                'json' => $data,
            ]);
            // Get the response code
            $response_code = $postResponse->getStatusCode();
        } catch (ConnectException $e) {
            $result_message = 'exec Aris Status Request FAILS ! status: ' . 404;
            $result_message = $result_message . '; msg: ' . $e->getMessage();
            $response_code = $e->getCode();
        } catch (RequestException $e) {
            $result_message = 'exec Aris Status Request FAILS ! status: ' . $e->getResponse()->getStatusCode();
            $result_message = $result_message . '; msg: ' . $e->getMessage();
            $response_code = $e->getResponse()->getStatusCode();
        } catch (\Exception $e) {
            $result_message = 'exec Aris Status Request FAILS ! status: ' . 0;
            $result_message = $result_message . '; msg: ' . $e->getMessage();
            $response_code = $e->getCode();
        } finally {
            if ($result_message !== "Success") {
                \Log::error($result_message);
            }
            return ['response_code' => $response_code, 'message' => $result_message];
        }
    }

    #region Manage Requests Treatments
    public function setRequestsToSendCount(bool $save = true) {
        $requests_to_send_count = 0;
        for ($i = $this->min_esim_id; $i <= $this->max_esim_id; $i++) {
            $requests_to_send_count += Esim::find($i) ? 1 : 0;
        }

        $this->requests_to_send_count = $requests_to_send_count;
        if ( $save ) {
            $this->save();
        }
    }
    public function incrementRequestsSentCount(int $nb_requests = 1, bool $save = true): void
    {
        $this->requests_sent_count += $nb_requests;
        $this->setRequestsSentRate($save);

        $this->setRequestsWaitingResultCount($save);

        ArisStatusRequestStat::incrementRequestsSentCount($nb_requests);
    }
    public function setRequestsSentRate(bool $save = true): void
    {
        if ($this->requests_to_send_count > 0) {
            $this->requests_sent_rate = round(($this->requests_sent_count / $this->requests_to_send_count) * 100, 2);
            if ($save) {
                $this->save();
            }
        }
    }
    public function setRequestsWaitingResultCount(bool $save = true): void {
        $this->requests_waiting_result_count = $this->requests_sent_count - $this->requests_received_count;
        $this->setRequestsWaitingResultRate($save);
    }
    public function setRequestsWaitingResultRate(bool $save = true): void
    {
        if ($this->requests_sent_count > 0) {
            $this->requests_waiting_result_rate = round(($this->requests_waiting_result_count / $this->requests_sent_count) * 100, 2);
            if ($save) {
                $this->save();
            }
        }
    }
    public function incrementRequestsReceivedCount(int $nb_requests = 1, bool $save = true): void
    {
        $this->requests_received_count += $nb_requests;
        $this->setRequestsReceivedRate($save);

        $this->setRequestsWaitingResultCount($save);

        ArisStatusRequestStat::incrementRequestsReceivedCount($nb_requests);
    }
    public function setRequestsReceivedRate(bool $save = true): void
    {
        if ($this->requests_to_send_count > 0) {
            $this->requests_received_rate = round(($this->requests_received_count / $this->requests_to_send_count) * 100, 2);
            if ($save) {
                $this->save();
            }
        }
    }
    #endregion

    /**
     * @return ArisStatusRequest|null
     */
    public static function getOneWaiting() : ?ArisStatusRequest {
        return ArisStatusRequest::whereRequestStatus(ArisStatusRequest::$STATUS_CODE_WAITING)->orderBy('id', 'asc')->first();
    }

    #region Read Settings Values
    public static function getMaxEsimsByRequest() {
        return config('Settings.arisrequest.max_esims.status');
    }
    public static function isRequestsActivated() {
        return config('Settings.arisrequest.activate');
    }
    #endregion

    #region Manage Stats
    private function decrementStatPreviousStatusRequests(int $nb_requests = 1): void
    {
        if ( $this->prev_request_status === self::$STATUS_CODE_WAITING ) {
            ArisStatusRequestStat::decrementWaitingRequestsCount($nb_requests);
        }
        if ( $this->prev_request_status === self::$STATUS_CODE_BUSY ) {
            ArisStatusRequestStat::decrementRequestsBusyCount($nb_requests);
        }
        if ( $this->prev_request_status === self::$STATUS_CODE_QUEUEING ) {
            ArisStatusRequestStat::decrementRequestsQueueingCount($nb_requests);
        }
    }
    #endregion

    public static function getById(int $id) : ?ArisStatusRequest {
        return ArisStatusRequest::find($id);
    }
    #endregion
}
