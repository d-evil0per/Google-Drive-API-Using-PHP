
<?php 
error_reporting(1);

require __DIR__ . '/google-drive-api/vendor/autoload.php';

$success_msg="";
$failure_msg="";



putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials.json');
$client = new Google_Client();
$client->addScope(Google_Service_Drive::DRIVE);
$client->useApplicationDefaultCredentials();

$driveService = new Google_Service_Drive($client);

if(isset($_POST['create_folder_btn']))
{

// $folderId=$_POST['folder_id'];
$folder_name=$_POST['folder_name'];
$folder_share_with=explode(",",$_POST['folder_share_with'].",".$_POST['admin']);
$folder_permission=$_POST['folder_permission'];

  //======= creating folder in root folder===================
$fileMetadata = new Google_Service_Drive_DriveFile(array(
    'name' => $folder_name,
    'parents' => array($folderId),
    'mimeType' => 'application/vnd.google-apps.folder'));
$file = $driveService->files->create($fileMetadata, array(
    'fields' => 'id'));
$success_msg.="<a target='_blank' href='https://drive.google.com/drive/u/1/folders/".$file->id."'>View Folder</a><br>";
// ================== ends ================================
// ===========    file sharing  ================================
$fileId = $file->id;
$driveService->getClient()->setUseBatch(true);
try {
    $batch = $driveService->createBatch();



$emailAddress=$folder_share_with;
$type=$folder_permission;


foreach ($emailAddress as $email) {

  $userPermission = new Google_Service_Drive_Permission(array(
        'type' => 'user',
        'role' => $type, 
        'emailAddress' => $email
    ));



    $request = $driveService->permissions->create(
        $fileId, $userPermission, array('fields' => 'id'));
    $batch->add($request, 'user');
      $results = $batch->execute();
}

  

    foreach ($results as $result) {
        if ($result instanceof Google_Service_Exception) {
            // Handle error
            // printf($result);
            $failure_msg.=$result;
        } else {
          $success_msg.="Permission ID: ".$result->id."<br>";
            // printf("Permission ID: %s\n", );
        }
    }
} finally {
    $driveService->getClient()->setUseBatch(false);
}


// ====================== ends ==================================
}




?>
<html>
  <head>
    <title>Google Drive API</title>
  <link href="https://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<style type="text/css">
  /* Credit to bootsnipp.com for the css for the color graph */
.colorgraph {
  height: 5px;
  border-top: 0;
  background: #c4e17f;
  border-radius: 5px;
  background-image: -webkit-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
  background-image: -moz-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
  background-image: -o-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
  background-image: linear-gradient(to right, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
}
</style>
  </head>
  
  <body>
    <h1 class="text-center">Google Drive API USING PHP</h1>
        <hr>
 <div class="container">

<div class="row">
    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    <form role="form" method="POST" action="<?=$post_url?>">
      <center>
        <h2>Create Folder</h2>
      <h3> <small>It's a demo to test automatic drive creation </small></h3>
      </center>
      <hr class="colorgraph">
  <?php if(isset($_POST['create_folder_btn'])) { 
     if($success_msg!="") { ?>
              <div class="alert alert-success text-center" style="width:500px;margin-top: 10px">
                <strong>Success!</strong> <?=$success_msg?><br>
                <p style="color:#000">Please Check Your Email, you must got an invitation email for the created drive</p>
                <p>
                  <span style="color:#000">Emails Associated With Names Are as follows: </span>
                  <center>
                  <table class="table" align="center">
                    <tr >
                    <th>Name</th>
                    <th>Email</th>
                  </tr>
                 
                  <tr>
                    <td>D-eviloper</td>
                    <td>rahul.dubey@d-eviloper.co.in</td>
                  </tr>
                
                  </table>
                  </center>
                </p>
              </div>
              <?php } elseif ($failure_msg!="") { ?>
              <div class="alert alert-danger text-center" style="width:500px;margin-top: 10px">
                <strong>Failure!</strong> <?=$failure_msg?>
              </div>
              <?php }  ?>
              <div class="alert alert-success text-center" style="width:500px;margin-top: 10px;cursor: pointer"  onclick="reload_func()">
                <strong> click to Refresh the page</strong>
              </div>
  <?php } else {?>
       <div class="form-group">
        <input type="text" name="folder_name" id="folder_name" class="form-control input-lg" placeholder="Folder Name" tabindex="3" required>
      </div>
      <div class="form-group">
       
        <center><small><p>All created folders are shared automatically with WeCode India </p></small></center>
        <input type="hidden" name="admin" value="wecode.b1010@gmail.com">
        
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
          <div class="form-group">
          
            <select name="folder_share_with" id="folder_share_with" class="form-control input-lg"  tabindex="5" required>
              <option value=" ">Shared With</option>
            
              <option value="rahul.dubey@d-eviloper.co.in">D-eviloper</option>
             
            </select>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
          <div class="form-group">
            
            <select class="form-control input-lg"  name="folder_permission" id="folder_permission" required>
                    <option value="">Select Permission Type</option>
                    <!-- <option value="owner">organizer/owner</option> -->
                    <option value="fileOrganizer">fileOrganizer</option>
                    <option value="writer">writer</option>
                    <option value="commenter">commenter</option>
                    <option value="reader">reader</option>
                  </select>
          </div>
        </div>
      </div>
  <?php } ?>
   
    
      
      <hr class="colorgraph">
      <div class="row">
        <div class="col-lg-12"><input type="submit" name="create_folder_btn" value="Create" class="btn btn-primary btn-block btn-lg" tabindex="7"></div>
        
      </div>
    </form>
  </div>
</div>

</div>
    
      
  </body>
      <?php if(isset($_POST['create_folder_btn']) ) { ?>
  <script type="text/javascript">
function reload_func(){ window.location="g-drive-api-simulate.php"; }
  </script>
<?php } ?>


</script>
</html>

