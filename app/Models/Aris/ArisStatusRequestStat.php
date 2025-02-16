<?php

namespace App\Models\Aris;

use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ArisStatusRequestStat
 *
 * @package App\Models\Aris
 * @property integer $id
 *
 * @property int|null $requests_to_send_count
 * @property int|null $waiting_requests_count
 *
 * @property int|null $requests_sent_count
 * @property int|null $requests_sent_rate
 *
 * @property int|null $requests_waiting_result_count
 * @property int|null $requests_waiting_result_rate
 *
 * @property int|null $requests_received_count
 * @property int|null $requests_received_rate
 *
 * @property int|null $requests_queueing_count
 * @property int|null $requests_queueing_rate
 *
 * @property int|null $requests_busy_count
 * @property int|null $requests_busy_rate
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ArisStatusRequestStat extends Model
{
    use HasFactory;

    #region Requests To Send Count
    private static function setRequestsToSendCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        $stat = ArisStatusRequestStat::getStat();
        $stat->requests_to_send_count += $nb_requests;

        if ( $save ) {
            $stat->save();
        }

        return $stat;
    }
    public static function incrementRequestsToSendCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setRequestsToSendCount($nb_requests, $save);
    }
    public static function decrementRequestsToSendCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setRequestsToSendCount((-1) * $nb_requests, $save);
    }
    #endregion

    #region Waiting Requests Count
    private static function updateWaitingRequestsCount(bool $save = true): ?ArisStatusRequestStat
    {
        $stat = ArisStatusRequestStat::getStat();
        $stat->requests_waiting_result_count = $stat->requests_sent_count - $stat->requests_received_count;
        $stat->setRequestsWaitingResultRate($save);

        return $stat;
    }
    private static function setWaitingRequestsCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        $stat = ArisStatusRequestStat::getStat();
        $stat->waiting_requests_count += $nb_requests;

        if ( $save ) {
            $stat->save();
        }
        $stat->setRequestsWaitingResultRate($save);

        return $stat;
    }
    public static function incrementWaitingRequestsCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setWaitingRequestsCount($nb_requests, $save);
    }
    public static function decrementWaitingRequestsCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setWaitingRequestsCount((-1) * $nb_requests, $save);
    }
    #endregion

    #region Requests Sent Count/Rate
    private static function setRequestsSentCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        $stat = ArisStatusRequestStat::getStat();
        $stat->requests_sent_count += $nb_requests;

        $stat->setRequestsSentRate( $save );

        return $stat;
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
    public static function incrementRequestsSentCount(int $nb_requests = 1, bool $save = true): void
    {
        self::setRequestsSentCount($nb_requests, $save);

        // then, we decrement the Requests to Send
        self::decrementRequestsToSendCount($nb_requests, $save)
            ?->setRequestsWaitingResultCount($save); // and, set (update) the Requests Waiting Result
    }
    public static function decrementRequestsSentCount(int $nb_requests = 1, bool $save = true): void
    {
        self::setRequestsSentCount((-1) * $nb_requests, $save);
    }
    #endregion

    #region Requests Waiting Result Count/Rate
    private function setRequestsWaitingResultCount(bool $save = true): void
    {
        //$stat = ArisStatusRequestStat::getStat();
        //$stat->requests_waiting_result_count += $nb_requests;
        $this->requests_waiting_result_count = $this->requests_sent_count - $this->requests_received_count;
        $this->setRequestsWaitingResultRate( $save );
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
    /*public static function incrementRequestsWaitingResultCount(int $nb_requests = 1, bool $save = true): void
    {
        self::setRequestsWaitingResultCount($nb_requests, $save);
    }
    public static function decrementRequestsWaitingResultCount(int $nb_requests = 1, bool $save = true): void
    {
        self::setRequestsWaitingResultCount((-1) * $nb_requests, $save);
    }*/
    #endregion

    #region Requests Received Count/Rate
    private static function setRequestsReceivedCount(int $nb_requests = 1, bool $save = true): void
    {
        $stat = ArisStatusRequestStat::getStat();
        $stat->requests_received_count += $nb_requests;

        $stat->setRequestsReceivedRate( $save );
    }
    public function setRequestsReceivedRate(bool $save = true): void
    {
        if ($this->requests_sent_count > 0) {
            $this->requests_received_rate = round(($this->requests_received_count / $this->requests_sent_count) * 100, 2);
            if ($save) {
                $this->save();
            }
        }
    }
    public static function incrementRequestsReceivedCount(int $nb_requests = 1, bool $save = true): void
    {
        self::setRequestsReceivedCount($nb_requests, $save);
    }
    public static function decrementRequestsReceivedCount(int $nb_requests = 1, bool $save = true): void
    {
        self::setRequestsReceivedCount((-1) * $nb_requests, $save);
    }
    #endregion

    #region Requests Queueing Count/Rate
    private static function setRequestsQueueingCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        $stat = ArisStatusRequestStat::getStat();
        $stat->requests_queueing_count += $nb_requests;

        $stat->setRequestsQueueingRate( $save );

        return $stat;
    }
    public function setRequestsQueueingRate(bool $save = true): void
    {
        if ($this->requests_to_send_count > 0) {
            $this->requests_queueing_rate = round(($this->requests_queueing_count / $this->requests_to_send_count) * 100, 2);
            if ($save) {
                $this->save();
            }
        }
    }
    public static function incrementRequestsQueueingCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setRequestsQueueingCount($nb_requests, $save);
    }
    public static function decrementRequestsQueueingCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setRequestsQueueingCount((-1) * $nb_requests, $save);
    }
    #endregion

    #region Requests Busy Count/Rate
    private static function setRequestsBusyCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        $stat = ArisStatusRequestStat::getStat();
        $stat->requests_busy_count += $nb_requests;

        $stat->setRequestsBusyRate( $save );

        return $stat;
    }
    public function setRequestsBusyRate(bool $save = true): void
    {
        if ($this->requests_to_send_count > 0) {
            $this->requests_busy_rate = round(($this->requests_busy_count / $this->requests_to_send_count) * 100, 2);
            if ($save) {
                $this->save();
            }
        }
    }
    public static function incrementRequestsBusyCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setRequestsBusyCount($nb_requests, $save);
    }
    public static function decrementRequestsBusyCount(int $nb_requests = 1, bool $save = true): ?ArisStatusRequestStat
    {
        return self::setRequestsBusyCount((-1) * $nb_requests, $save);
    }
    #endregion

    public static function isThereRequestsToBeProcessed() : bool {
        $stat = self::getStat();

        if ( $stat->requests_to_send_count > 0 ) {
            return true;
        }
        if ( $stat->requests_queueing_count > 0 ) {
            return true;
        }
        if ( $stat->requests_busy_count > 0 ) {
            return true;
        }

        return false;
    }
    public static function isMaxRequestsToBeProcessedReached() : bool {
        $stat = self::getStat();

        return $stat->requests_to_send_count >= self::getMaxRunningRequests();
    }

    public static function isMaxWaitingRequestsReached(): bool {
        $stat = self::getStat();
        $requests_waiting_result_count_all =  $stat->requests_waiting_result_count + $stat->requests_queueing_count + $stat->requests_busy_count;
        /*
        ArisStatusRequest::
        where( 'request_status', ArisStatusRequest::$STATUS_CODE_WAITING )
            ->orWhere( 'request_status', ArisStatusRequest::$STATUS_CODE_QUEUEING )
            ->orWhere( 'request_status', ArisStatusRequest::$STATUS_CODE_BUSY )
            ->sum( 'requests_waiting_result_count' );
        */
        $max_waiting_requests = self::getMaxWaitingRequests();

        $result = ( $requests_waiting_result_count_all >= $max_waiting_requests );

        if ( $result ) {
            Log::info("ArisStatusRequest - Max Waiting (". $max_waiting_requests .") Reached !");
            Log::info("MaxWaitingRequests Treshold: " . $max_waiting_requests . ", requests waiting All count: " . $requests_waiting_result_count_all);
        }

        return $result;
    }

    public static function getMaxRunningRequests() {
        $setting_value = config('Settings.arisrequest.max_running.status');
        if ( empty($setting_value) ) {
            return Setting::getByFullPath("arisrequest.max_running.status")->value;
        } else {
            return $setting_value;
        }
    }
    public static function getMaxWaitingRequests() {
        //return config('Settings.arisrequest.max_waiting.status');
        $setting_value = config('Settings.arisrequest.max_waiting.status');
        if ( empty($setting_value) ) {
            return Setting::getByFullPath("arisrequest.max_waiting.status")->value;
        } else {
            return $setting_value;
        }
    }

    public static function getStat() : ?ArisStatusRequestStat {
        if ( ArisStatusRequestStat::count('id') > 0 ) {
            return ArisStatusRequestStat::first();
        }
        return ArisStatusRequestStat::create();
    }
}
