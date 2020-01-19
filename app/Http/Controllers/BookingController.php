<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;
    private  $data;
    private $requestAuthenticatedUser;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        //removed unnecessary calling of getUsersJobs without verification of user role;
        if($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $this->repository->getAll($request);
        }
        else if($user_id = $request->get('user_id')){
            $response = $this->repository->getUsersJobs($user_id);
        }
        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id){
        $job = $this->repository->with('translatorJobRel.user')->find($id);
        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request){
        $this->getAllRequest($request);
        $response = $this->repository->store($request->__authenticatedUser,  $this->data);
        return response($response);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $this->getAllRequest($request);
        $this->getRequestAuthenticatedUser($request);
        $response = $this->repository->updateJob($id, array_except( $this->data, ['_token', 'submit']), $this->requestAuthenticatedUser);
        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $this->getAllRequest($request);
        $response = $this->repository->storeJobEmail( $this->data);
        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        if($user_id = $request->get('user_id')) {
            $response = $this->repository->getUsersJobsHistory($user_id, $request);
            return response($response);
        }
        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
         $this->getAllRequest($request);
         $this->getRequestAuthenticatedUser($request);
        $response = $this->repository->acceptJob( $this->data, $this->requestAuthenticatedUser);
        return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $this->data = $request->get('job_id');
        $this->getRequestAuthenticatedUser($request);
        $response = $this->repository->acceptJobWithId( $this->data, $this->requestAuthenticatedUser);
        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $this->getAllRequest($request);
        $this->getRequestAuthenticatedUser($request);
        $response = $this->repository->cancelJobAjax( $this->data, $this->requestAuthenticatedUser);
        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $this->getAllRequest($request);
        $response = $this->repository->endJob( $this->data);
        return response($response);
    }

    public function customerNotCall(Request $request)
    {
        $this->getAllRequest($request);
        $response = $this->repository->customerNotCall( $this->data);
        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $this->getAllRequest($request);
        $this->getRequestAuthenticatedUser($request);
        $response = $this->repository->getPotentialJobs($this->requestAuthenticatedUser);
        return response($response);
    }

    public function distanceFeed(Request $request)
    {
        $distance = "";
        $time = "";
        $session = "";
        $flagged = 'no';
        $manually_handled = 'no';
        $by_admin = 'no';
        $admincomment = "";
        $this->getAllRequest($request);
        if (isset( $this->data['distance']) &&  $this->data['distance'] != "")
            $distance =  $this->data['distance'];

        if (isset( $this->data['time']) &&  $this->data['time'] != "")
            $time =  $this->data['time'];

        if (isset( $this->data['jobid']) &&  $this->data['jobid'] != "")
            $jobid =  $this->data['jobid'];
            
        if (isset( $this->data['session_time']) &&  $this->data['session_time'] != "")
            $session =  $this->data['session_time'];
         
        if ( $this->data['flagged'] == 'true') 
            if( $this->data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        
        if ( $this->data['manually_handled'] == 'true') 
            $manually_handled = 'yes';
        
        if ( $this->data['by_admin'] == 'true') 
            $by_admin = 'yes';
        
        if (isset( $this->data['admincomment']) &&  $this->data['admincomment'] != "") 
            $admincomment =  $this->data['admincomment'];
        
        if ($time || $distance) {
            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
        }
        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));
        }
        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        $this->getAllRequest($request);
        $response = $this->repository->reopen( $this->data);
        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $this->getAllRequest($request);
        $job = $this->repository->find( $this->data['jobid']);
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');
        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
         $this->getAllRequest($request);
        $job = $this->repository->find( $this->data['jobid']);
        $job_data = $this->repository->jobToData($job);
        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }
    private function getAllRequest($request=null){
        $this->data=$request->all();
    }
    private function getRequestAuthenticatedUser($request=null)
    {
        $this->requestAuthenticatedUser=$request->__authenticatedUser;
    }

}
