<?php


namespace App\Http\Controllers;

use App\Clients;
use App\Exports\Overview;
use App\Exports\QuestioneryExport;
use App\Projects;
use App\Questioner;
use App\Settings;
use App\User;
use App\Course;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use PDF;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

class QuestionerController extends Controller
{
    public function uploadContent(Request $request)
    {
            // In case the uploaded file path is to be stored in the database
            $filepath = public_path('storage/Questioner_new.csv');
            // Reading file
            $file = fopen($filepath, "r");
            $importData_arr = array(); // Read through the file and store the contents as an array
            $i = 0;
            //Read the contents of the uploaded file
            while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
                $num = count($filedata);
            // Skip first row (Remove below comment if you want to skip the first row)
                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file); //Close after reading

            $j = 0;

            try {

                foreach ($importData_arr as $importData) {
                    $j++;
                    $user_id ="";

                    if(isset($importData[2])) {
                        print_r($importData[2]);
                        $first_name = explode(" ", $importData[2]);
                        $rows = User::where('firstname', $first_name[0])->get();
                        foreach ($rows as $row) {
                            $user_id = ($row->id);
                            Questioner::create([
                                'date' => $importData[0],
                                'client_name' => $importData[1],
                                'client_id' => null,
                                'teacher_name' => $importData[2],
                                'user_id' => $user_id,
                                'encourage_the_course' => $importData[3],
                                'encourage_the_homework' => $importData[4],
                                'content_is_clear' => $importData[5],
                                'specific_questions_clear' => $importData[6],
                                'content_is_interesting' => $importData[7],
                                'well_prepared' => $importData[8],
                                'help_you_improve' => $importData[9],
                                'how_coach_can_improve' => $importData[10],
                                'strengths_of_coach' => $importData[11]
                            ]);
                        }
                    }
//                    if(isset($importData[1])){
//                        $clients_rows = Clients::where([
//                            'name'  =>  $importData[1]
//                        ])->get();
//
//
//                        foreach ($clients_rows as $clr) {
//                            print_r($clr);
//                            $questionerUpdate = Questioner::where([
//                                'client_name'  =>  $clr->name
//                            ]);
//                            $questionerUpdate->update([
//                                'client_id' => $clr->id,
//                            ]);
//                        }
//                    }


                    print_r($j."</br>");

                }
            } catch (Exception $e) {
                echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            }


            return response()->json([
                'message' => "$j records successfully uploaded"
            ]);
    }
    public function show(Request $request)
    {

        $model = new Questioner();
        $user = new User();
        $settings = new Settings();

        if($request->get("export_rate_client")){
            //Export report (pdf)
            $model = new Questioner();
            $user  = new User();
            $id_client = \App\Clients::ClientName(($request->get("clients")))["id"];
            $client_name =$request->get("client_name");
            $project_id = \App\Questioner::getClientIdProjectForFeedbacks($id_client, $request->get('clientteachers'));

            if (\App\Projects::getClientInfoProjectForFeedbacks($project_id)){
                $ref = \App\Projects::getClientInfoProjectForFeedbacks($project_id)->reference;
                $data = (array(
                    "client_id"=>\App\Clients::ClientName(($request->get("clients")))["id"],
                    "client_name"=>\App\Clients::ClientName(($request->get("clients")))["name"],
                    "reference"=> $ref,
                    "teacherforproject"=>$model->getTeacherNameFromQuestioner($project_id, $id_client, $client_name, $request->get('teacher_name'))["teacher_name"],
                    "datefeedback"=>$model->getTeacherNameFromQuestioner($project_id, $id_client, $client_name, $request->get('teacher_name'))["date_feedback"],
                    "datestart"=>\App\Projects::getClientInfoProjectForFeedbacks($project_id)->start_date,
                    "dateend"=>\App\Projects::getClientInfoProjectForFeedbacks($project_id)->end_date,
                    "hours"=>\App\Projects::getClientInfoProjectForFeedbacks($project_id)->hoursf2f,
                    "hoursonline"=>\App\Projects::getClientInfoProjectForFeedbacks($project_id)->hours_online,
                    "first"=>\App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'),$request->get('toclient'), 'encourage_the_course', $client_name),
                    "second"=>\App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'),$request->get('toclient'), 'encourage_the_homework', $client_name),
                    "third"=>\App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'),$request->get('toclient'), 'content_is_clear', $client_name),
                    "fours"=>\App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'),$request->get('toclient'), 'specific_questions_clear', $client_name),
                    "five"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('clientteachers'), $request->get('fromclient'),$request->get('toclient'), 'content_is_interesting', $client_name),
                    "six"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('clientteachers'), $request->get('fromclient'),$request->get('toclient'), 'well_prepared', $client_name),
                    "seven"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('clientteachers'), $request->get('fromclient'),$request->get('toclient'), 'help_you_improve', $client_name),
                ));

                ini_set('max_execution_time', '300');
                $folder = storage_path('app/public/report/' . $data["reference"]);
                if (!Storage::disk('report')->exists($data["reference"])) {
                    Storage::disk('report')->makeDirectory($data["reference"], 0775, true); //creates directory
                }
                print_r(1);
                $pdf = PDF::loadView('questioner.questioner_client_pdf', compact("data"));

                return  $pdf->save($folder . "/" . $data["reference"]. ".pdf")->download($data["reference"]. ".pdf");
            }
        } else if ($request->get("export_exele_client")) {
            $model = new Questioner();
            $user = new User();
            $id_client = \App\Clients::ClientName(($request->get("clients")))["id"];
            $client_name = $request->get("client_name");
            $project_id = \App\Questioner::getClientIdProjectForFeedbacks($id_client, $request->get('clientteachers'));
            if (\App\Projects::getClientInfoProjectForFeedbacks($project_id)) {
                $ref = \App\Projects::getClientInfoProjectForFeedbacks($project_id)->reference;


                $data = (array(
                    "client_id" => \App\Clients::ClientName(($request->get("clients")))["id"],
                    "client_name" => \App\Clients::ClientName(($request->get("clients")))["name"],
                    "reference" => $ref,
                    "teacherforproject" => $model->getTeacherNameFromQuestioner($project_id, $id_client,$client_name, $request->get('teacher_name'))["teacher_name"],
                    "datefeedback" => $model->getTeacherNameFromQuestioner($project_id, $id_client, $client_name, $request->get('teacher_name'))["date_feedback"],
                    "datestart" => \App\Projects::getClientInfoProjectForFeedbacks($project_id)->start_date,
                    "dateend" => \App\Projects::getClientInfoProjectForFeedbacks($project_id)->end_date,
                    "hours" => \App\Projects::getClientInfoProjectForFeedbacks($project_id)->hoursf2f,
                    "hoursonline" => \App\Projects::getClientInfoProjectForFeedbacks($project_id)->hours_online,
                    "first" => \App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('clientteachers'), $request->get('fromclient'), $request->get('toclient'), 'encourage_the_course',  $client_name),
                    "second" => \App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'), $request->get('toclient'), 'encourage_the_homework',  $client_name),
                    "third" => \App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'), $request->get('toclient'), 'content_is_clear',  $client_name),
                    "fours" => \App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'), $request->get('toclient'), 'specific_questions_clear',  $client_name),
                    "five" => \App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'), $request->get('toclient'), 'content_is_interesting',  $client_name),
                    "six" => \App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'), $request->get('toclient'), 'well_prepared',  $client_name),
                    "seven" => \App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('clientteachers'), $request->get('fromclient'), $request->get('toclient'), 'help_you_improve',  $client_name),
                ));
                return Excel::download(new QuestioneryExport($data), 'questioner_import ' . date('d-m-Y') . '.xlsx');
            }
        }

        return view('questioner.questioner',compact('model','user','settings'));
    }

    public function getinfoforteacherrate(Request $request)
    {
        $model = new Questioner();
        $user  = new User();
        if($request->has("teacher")){
//            //moderation for comments
            if($request->get("id_for_moderation")!=0){
                $questionerUpdate = Questioner::where([
                    'id'  =>  $request->get("id_for_moderation")
                ]);
                if($request->get("comments") == "negative"){
                    if($request->get("method") == "minus"){
                        $questionerUpdate->update([
                            'active_negative' => 2,
                        ]);
                    } else if($request->get("method") == "plus"){
                        $questionerUpdate->update([
                            'active_negative' => 1,
                        ]);
                    }
                } else if($request->get("comments") == "positive"){
                    if($request->get("method") == "minus"){
                        $questionerUpdate->update([
                            'active_positive' => 2,
                        ]);
                    } else if($request->get("method") == "plus"){
                        $questionerUpdate->update([
                            'active_positive' => 1,
                        ]);
                    }
                }
            }
            //
            $teacher_feedback = $model->GetTeacherFeedbacksCount($request->get("teacher"), $request->get("from"), $request->get("to"));
            if($request->get("teacher") == -1){
                $teacher_name = "All teachers";
            } else {
                $teacher_name = $user->GetTeacherFirstLastName($request->get("teacher"));
            }

            $persentEncour = $model->getQuestionRateEncourForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentEncourHomework = $model->getQuestionRateEncourHomeworkForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentClearContent = $model->getQuestionRateClearContentForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentSpecificQuestions = $model->getQuestionRateSpecificQuestionsForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentContentInteresting = $model->getQuestionRateContentInterestingForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentWellPrepared = $model->getQuestionRateWellPreparedForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentHelpYouImprove = $model->getQuestionRateHelpYouImproveForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentPositiveComments = $model->getQuestionRateStrengthsOfCoachForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
            $persentNegativeComments = $model->getQuestionRateNegativeForTeacher($request->get("teacher"), $request->get("from"), $request->get("to"));
        }
        return json_encode(array("persentEncour"=>$persentEncour,
                                 "persentEncourHomework"=>$persentEncourHomework,
                                 "persentClearContent"=>$persentClearContent,
                                 "persentSpecificQuestions"=>$persentSpecificQuestions,
                                 "persentContentInteresting"=>$persentContentInteresting,
                                 "persentWellPrepared"=>$persentWellPrepared,
                                 "persentHelpYouImprove"=>$persentHelpYouImprove,
                                 "persentPositiveComments"=>$persentPositiveComments,
                                 "persentNegativeComments"=>$persentNegativeComments,
                                 "teacher_name"=>$teacher_name,
                                 "teacher_feedback"=> $teacher_feedback));
    }


    public function getinfoforquestionrate(Request $request)
    {
        $model = new Questioner();
        $user  = new User();
        $question_rate = [];
        $teacher_feedback = $model->GetTeacherFeedbacksByQuestionCount($request->get("teacher"),  $request->get("questions"),  $request->get("from"), $request->get("to"));
        if($request->get("teacher") == 0){
            $teacher_name = "All HOT teachers";
        } else {
            $teacher_name = $user->GetTeacherFirstLastName($request->get("teacher"));
        }
        $membersofteam = $model->getMembersOfTeam($request->get("teacher"), \App\User::GethotteachersId());
        foreach ($membersofteam as $member) {
            $question_rate[] = array("firstname"=>$member["firstname"], "rate"=>$model->getPersentByQuestion($request->get("questions"), $member["user"], $request->get("from"), $request->get("to")));
        }

        return json_encode(array(
            "teacher_name"=>$teacher_name,
            "question_rate"=>$question_rate,
            "teacher_feedback"=> $teacher_feedback));
    }

    public function getinfoforclientrate(Request $request)
    {
        $model = new Questioner();
        $user  = new User();
        $id_client = \App\Clients::ClientName(($request->get("client")))["id"];
        $client_name = $request->get("client_name");
        $client_id = \App\Questioner::GetClientsId($id_client);
        $project_id = \App\Questioner::getClientIdProjectForFeedbacks($client_id, $request->get('teacher'));

        $result_reference = \App\Projects::getClientInfoProjectForFeedbacks($project_id);

        if($result_reference) {
            $ref = \App\Projects::getClientInfoProjectForFeedbacks($project_id)->reference;
            $start_date = \App\Projects::getClientInfoProjectForFeedbacks($project_id)->start_date;
            $end_date =\App\Projects::getClientInfoProjectForFeedbacks($project_id)->end_date;
            $hours = \App\Projects::getClientInfoProjectForFeedbacks($project_id)->hoursf2f;
            $online = \App\Projects::getClientInfoProjectForFeedbacks($project_id)->hours_online;
        } else {
            $ref ="";
            $start_date = "";
            $end_date ="";
            $hours ="";
            $online = "";
        }


        return json_encode(array(
            "client_name"=>\App\Clients::ClientName(($request->get("client")))["name"],
            "reference"=> $ref,
            "teacherforproject"=>$model->getTeacherNameFromQuestioner($project_id, $id_client, $client_name, $request->get('teacher_name'))["teacher_name"],
            "datefeedback"=>$model->getTeacherNameFromQuestioner($project_id, $id_client, $client_name, $request->get('teacher_name'))["date_feedback"],
            "datestart"=>$start_date,
            "dateend"=>$end_date,
            "hours"=>$hours,
            "hoursonline"=>$online,
            "first"=>\App\Questioner::RateForClientQuestion($project_id, $id_client, $request->get('teacher'), $request->get('from'),$request->get('to'), 'encourage_the_course',  $client_name),
            "second"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('teacher'), $request->get('from'),$request->get('to'), 'encourage_the_homework',  $client_name),
            "third"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('teacher'), $request->get('from'),$request->get('to'), 'content_is_clear',  $client_name),
            "fours"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('teacher'), $request->get('from'),$request->get('to'), 'specific_questions_clear',  $client_name),
            "five"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('teacher'), $request->get('from'),$request->get('to'), 'content_is_interesting',  $client_name),
            "six"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('teacher'), $request->get('from'),$request->get('to'), 'well_prepared',  $client_name),
            "seven"=>\App\Questioner::RateForClientQuestion($project_id, $id_client,  $request->get('teacher'), $request->get('from'),$request->get('to'), 'help_you_improve',  $client_name),
            ));
    }


    //open questioner
    public function QuestionerOpen($hashid,$user_id, $client_name, Request $request)
    {
        try{
            $id_decrypted = Crypt::decryptString($hashid);
        }catch(Throwable $e){
            print_r($e);
            abort(404);
        }
        $project = Projects::findOrFail($id_decrypted);
        $course = Course::getCourseName();

        if($request->has('sendquestioner')){
            $client = Clients::find($project->client_name);

            $user = new User();
            $settings = new Settings();
            $clients_rows = Clients::where([
                'name'  =>  $request->get('client_name'),
            ])->get();
            foreach ($clients_rows as $cl){
                $array = $cl->id;
            }
            $teachers = (\App\User::GetUserById($request->get('teachers')));
            $this->validate($request,
                [
                    'client_name' => 'required',
                    'teachers' => 'required',
                    'number' => 'required',
                    'number1' => 'required',
                    'number2' => 'required',
                    'number3' => 'required',
                    'number4' => 'required',
                    'number5' => 'required',
                    'positive' => 'required',
                    'negative' => 'required',
                    'hashid' => 'required',
                ]);



            try {
                $questioner = Questioner::create([
                    'client_name'            => $request->get('client_name'),
                    'client_id' => $array,
                    'teacher_name'         => $teachers,
                    'date'         => now(),
                    'course'         => $request->get('course'),
                    'user_id'         => $request->get('user_id'),
                    'encourage_the_course'       => $request->get('number'),
                    'encourage_the_homework'       => $request->get('number1'),
                    'content_is_clear'       => $request->get('number2'),
                    'specific_questions_clear'       => $request->get('number3'),
                    'content_is_interesting'       => $request->get('number4'),
                    'well_prepared'       => $request->get('number5'),
                    'help_you_improve'       => $request->get('number6'),
                    'how_coach_can_improve'       => $request->get('negative'),
                    'strengths_of_coach'       => $request->get('positive'),
                    'hashid'       =>  Crypt::decryptString($request->get('hashid')),
                ]);
            } catch (Exception $e) {
                echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            }

        }
        return view('questioner.questioner_report',compact('project', 'user_id','course', 'client_name', "hashid"));
    }

}
