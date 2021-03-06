<?php
include('../../lib/core/loader.php');

if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    include('../../lib/frontend/top.php');
    echo '<h2>404 - Invalid ticket</h2>';
    include('../../lib/frontend/bottom.php');
    die();
}

if (isset($_GET['lang']) && strlen($_GET['lang']) == 2){
    $infolang = $_GET['lang'];
} else {
    $infolang = 'en';
}

$title = 'Ticket '.$_GET['id'];

if(isset($_GET['action']) && $_GET['action'] == 'Notify' && is_numeric($_GET['id'])) {
    $filter = array(
                    'Ticket'  => $_GET['id'],
                   );

    if (reportSend($filter)) {
        $PostMessage = "A new notification was sent to contacts";
    } else {
        $PostMessage = "Failed sending notification to contacts";
    }

}
if(isset($_GET['action']) && $_GET['action'] == 'UpdateContact' && is_numeric($_GET['id'])) {
    ReportContactupdate($_GET['id']);
    $PostMessage = "Customer information is updated";
}
if(isset($_GET['action']) && $_GET['action'] == 'MarkIgnored' && is_numeric($_GET['id'])) {
    reportIgnored($_GET['id']);
    $PostMessage = "This ticket has been marked as customer ignored. The listed contacts will no longer receive reports on this ticket";
}
if(isset($_GET['action']) && $_GET['action'] == 'MarkResolved' && is_numeric($_GET['id'])) {
    reportResolved($_GET['id']);
    $PostMessage = "This ticket has been marked as customer resolved. Any new reports will resend a notification directly";
}
if(isset($_GET['action']) && $_GET['action'] == 'MarkClosed' && is_numeric($_GET['id'])) {
    reportClosed($_GET['id']);
    $PostMessage = "This ticket has been marked as closed. Any new reports will open a new ticket";
}

$report = reportGet($_GET['id']);

if (!$report) {
    include('../../lib/frontend/top.php');
    echo '<h2>404 - Invalid ticket</h2>';
    include('../../lib/frontend/bottom.php');
    die();
}

// View or download evidence
if(isset($_GET['action']) && $_GET['action'] == 'DownloadEvidence' && is_numeric($_GET['EvidenceID'])) {
    if ($eml = evidenceGet($_GET['EvidenceID'])) {
        header('Content-Type: message/rfc822');
        header('Content-Transfer-Encoding: Binary'); 
        header("Content-disposition: attachment; filename=\"${_GET['EvidenceID']}.eml\""); 
        echo $eml['Data'];
        die();
    }
} 
if (isset($_GET['action']) && $_GET['action'] == 'ViewEvidence' && is_numeric($_GET['EvidenceID'])) {
    if ($eml = evidenceGet($_GET['EvidenceID'])) {
        include('../../lib/frontend/top.php');
        echo '<pre>';
        echo htmlentities($eml['Data']);
        echo '</pre>';
        include('../../lib/frontend/bottom.php');
        die();
    }
}

include('../../lib/frontend/top.php');

$labelClass = array(
    'ABUSE'     => 'warning',
    'INFO'      => 'info',
    'ALERT'     => 'danger',
    'OPEN'      => 'warning',
    'CLOSED'    => 'info',
    'ESCALATED' => 'danger',
    'NO'        => 'warning',
    'YES'       => 'info',
    '0'         => 'warning',
    '1'         => 'info',
); 

if(isset($PostMessage)) {
    echo "<body onLoad=\"alert('${PostMessage}')\">";
}
?>

<div class="row">
    <div class="col-md-6">
        <dl class="dl-horizontal">
            <dt>IP address</dt>
            <dd><?php echo "<a href='reports.php?IP=${report['IP']}'>${report['IP']}</a>"; ?></dd>

            <?php 
                $reverse = gethostbyaddr($report['IP']);
                if ($reverse != $report['IP'] && $reverse !== false) {
            ?>
            <dt>Reverse DNS</dt>
            <dd><?php echo gethostbyaddr($report['IP']); ?></dd>
            <?php } ?>

            <?php if (!empty($report['Domain'])) { ?>
            <dt>Domain</dt>
            <dd><?php echo $report['Domain']; ?></dd>
            <?php } ?>

            <?php if (!empty($report['URI'])) { ?>
            <dt>URI</dt>
            <dd><?php echo $report['URI']; ?></dd>
            <?php } ?>

            <dt>Classification</dt>
            <dd><?php echo "<a href='reports.php?Class=${report['Class']}'>${report['Class']}</a>"; ?></dd>

            <dt>Source</dt>
            <dd><?php echo "<a href='reports.php?Source=${report['Source']}'>${report['Source']}</a>"; ?></dd>

            <dt>Type</dt>
            <dd><?php echo "<span class='label label-${labelClass[$report['Type']]}'><a href='reports.php?Type=${report['Type']}'>${report['Type']}</a></span>"; ?></dd>

            <dt>Ticket status</dt>
            <dd><?php echo "<span class='label label-${labelClass[$report['Status']]}'><a href='reports.php?Type=${report['Status']}'>${report['Status']}</a></span>"; ?></dd>

<?php
    if (SELF_HELP_URL != "") {
        $token    = md5("${report['ID']}${report['IP']}${report['Class']}");
        $tokenurl = SELF_HELP_URL . "?id=${report['ID']}&token=" . $token;
        echo "    <dt>Self Help URL</dt>" . PHP_EOL;
        echo "    <dd><a target='_blank' href = '${tokenurl}'>${tokenurl}</a></dd>" . PHP_EOL;
    }
?>

        </dl>
    </div>
    <div class="col-md-6">
        <div class='btn-group pull-right'>
            <a href='?id=<?php echo $_GET['id']; ?>&action=UpdateContact' class='btn btn-default btn-sm' title='Try to resolve or update the customer information'>Update customer</a>
            <a href='?id=<?php echo $_GET['id']; ?>&action=Notify' class='btn btn-default btn-sm' title='Resend a notification to customer for this ticket'>Send notification</a>
            <a href='?id=<?php echo $_GET['id']; ?>&action=MarkIgnored' class='btn btn-default btn-sm' title='Set status to customer ignored'>Ignore report</a>
            <a href='?id=<?php echo $_GET['id']; ?>&action=MarkResolved' class='btn btn-default btn-sm' title='Set status to customer resolved'>Mark resolved</a>
            <a href='?id=<?php echo $_GET['id']; ?>&action=MarkClosed' class='btn btn-default btn-sm' title='Set status to closed. Another incoming abuse mail will result in a new ticket!'>Close ticket</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h2>Customer Information</h2>

        <dl class="dl-horizontal">
            <dt>Customer Code</dt>
            <dd><?php echo "<a href='reports.php?CustomerCode=${report['CustomerCode']}'>${report['CustomerCode']}</a>"; ?></dd>

            <dt>Customer Name</dt>
            <dd><?php echo "<a href='reports.php?CustomerName=${report['CustomerName']}'>${report['CustomerName']}</a>"; ?></dd>

            <dt>Contact(s)</dt>
            <dd><?php echo $report['CustomerContact']; ?></dd>

            <dt>Resolved</dt>
            <dd><?php echo "<span class='label label-${labelClass[$report['CustomerResolved']]}'>". ($report['CustomerResolved'] ? 'YES' : 'NO') . "</span>"; ?></dd>

            <dt>Ignored</dt>
            <dd><?php echo "<span class='label label-${labelClass[$report['CustomerResolved']]}'>". ($report['CustomerIgnored']  ? 'YES' : 'NO') . "</span>"; ?></dd>
        </dl>
    </div>
    <div class="col-md-6">
        <h2>Report Status</h2>

        <dl class="dl-horizontal">
            <dt>Seen</dt>
            <dd><?php echo $report['ReportCount']; ?>&times;</dd>

            <dt>First Seen</dt>
            <dd><?php echo date("d-m-Y H:i", $report['FirstSeen']); ?></dd>

            <dt>Last Seen</dt>
            <dd><?php echo date("d-m-Y H:i", $report['LastSeen']); ?></dd>

            <dt>Notifications</dt>
            <dd><?php echo $report['NotifiedCount']; ?>&times;</dd>

            <dt>Last notification</dt>
            <dd>
            <?php
                if ($report['LastNotifyReportCount'] === '0') {
                    echo "Never"; 
                } else {
                    echo "At count ". $report['LastNotifyReportCount'] ." on ". date("d-m-Y H:i", $report['LastNotifyTimestamp']); 
                }
            ?>
            </dd>
        </dl>
    </div>
</div>

</dl>

<h2>Information</h2>

<?php
    $info_array = json_decode($report['Information'], true);
    if (empty($info_array)) {
            echo '<p>No information found</p>';
    } else {
        echo '<dl class="dl-horizontal">';
        foreach($info_array as $field => $value) {
            echo "<dt>${field}</dt>";
            echo "<dd>".htmlentities($value)."</dd>";
        }
        echo '</dl>';
    }
?>

<?php
$infotext = infotextGet($infolang, $report['Class']);
if ($infotext) {
?>
    <p style="margin: 2em 0;"><a class="btn btn-default" data-toggle="collapse" href="#infotext" aria-expanded="false" aria-controls="infotext">Show information text</a></p>
    <div class="collapse" id="infotext">
        <div class="well"><?php echo$infotext; ?></div>
    </div>
<?php } ?>

<?php
$evidences = evidenceList($_GET['id']);

if (!empty($evidences)) {
?>
<p style="margin: 2em 0;"><a class="btn btn-default" data-toggle="collapse" href="#evidencetable" aria-expanded="false" aria-controls="evidencetable">Show linked evidence</a></p>

<div class="collapse" id="evidencetable">

<h2>Evidence</h2>

<table class="table table-striped table-condensed">
    <thead>
        <tr>
          <th width='200'>Date</th>
          <th width='300'>Sender</th>
          <th>Subject</th>
          <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
<?php
    foreach($evidences as $nr => $evidence) {
        echo "
            <tr>
              <td>${evidence['LastModified']}</td>
              <td>". htmlentities($evidence['Sender']) ."</td>
              <td>". htmlentities($evidence['Subject']) ."</td>
              <td>
                    <div class='btn-group pull-right'>
                        <a href='?action=ViewEvidence&EvidenceID=${evidence['ID']}&id=${_GET['id']}' class='btn btn-default btn-sm' title='View EML file' target='_blank'>View<a/>
                        <a href='?action=DownloadEvidence&EvidenceID=${evidence['ID']}&id=${_GET['id']}' class='btn btn-default btn-sm' title='Download EML file'>Download<a/>
                    </div>
              </td>
            </tr>
        ";
    }
?>
    </tbody>
</table>
</div>
<?php } // End of Evidence section ?>

<?php
// Start Notes section
if (NOTES == true) {

if(!empty($_POST['action']) && $_POST['action'] == 'addNote') {
    if(!empty($_SERVER['REMOTE_USER'])) {
        $submittor = $_SERVER['REMOTE_USER'];
    } else {
        $submittor = "Abusedesk";
    }
    if (!empty($_POST['Note'])) reportNoteAdd($submittor, $_POST['id'], htmlentities($_POST['Note']));
}
if(!empty($_GET['action']) && $_GET['action'] == 'delNote' && is_numeric($_GET['noteid'])) {
    reportNoteDelete($_GET['noteid']);
}
?>


<h2>Notes</h2>

<table class="table table-striped table-condensed">
    <thead>
        <tr>
          <th width='200'>Date</th>
          <th width='300'>Submittor</th>
          <th>Note</th>
          <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
<?php
$filter  = "AND ReportID = ${_GET['id']} ORDER BY Timestamp DESC";
$notes = reportNoteList($filter);

foreach($notes as $nr => $note) {
    echo "
        <tr>
          <td>".date("Y-m-d H:i:s", $note['Timestamp'])."</td>
          <td>${note['Submittor']}</td>
          <td>${note['Text']}</td>
          <td>
              <div class='btn-group pull-right'>
                  <a href='?action=delNote&id=${_GET['id']}&noteid=${note['ID']}' class='btn btn-default btn-sm' title='Delete note' onclick='return confirm(\"Are you sure you want to delete this note?\");'>Delete</a>
              </div>
          </td>
        </tr>
    ";
}
?>
    </tbody>
</table>

<br>

<form method='POST' action="ticket.php?id=<?php echo $_GET['id']; ?>">
    <input type='hidden' name='action' value='addNote'>
    <input type='hidden' name='id' value='<?php echo $_GET['id']; ?>'>
    <div class="row">
        <div class="col-md-6 form-group form-group-sm">
            <label for='Note'>Add note : </label>
            <textarea rows="4" cols="80" name='Note'></textarea>
            <button type='submit' class="btn btn-primary">Save</button>
        </div>
    </div>
</form>

<?php 
} // End Notes section 

include('../../lib/frontend/bottom.php');
?>
