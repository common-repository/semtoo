<?php
/*
Plugin Name: Semtoo
Plugin URI: http://www.semtoo.com
Description: Semtoo
Version: 1.0
Author: Marcel Gladebeck
Author URI: http://www.marcelgladebeck.de
Min WP Version: 1.5
Max WP Version: 2.0.4
*/

class Semtoo {

    public $account;
    public $api_key;
    public $api_id;

    public function __construct() {

	$this->api_key = get_option( 'semtoo_api_key' );
	$this->api_id = get_option( 'semtoo_key_id' );

	$headers = array ('Authorization' => 'Token '.$this->api_key);
	$url = 'https://www.semtoo.com/api.php?action=account&id='.$this->api_id;
	$response = wp_remote_get( $url, array ('method' => 'GET', 'headers' => $headers) );

	$this->account = explode(";", $response['body']);
    }

    /**
     * Plugin's interface
     *
     * @return void
     */
    function configure() {

	if (!$this->api_key) {
	  echo '<h2>Es ist noch kein API-Key vorhanden!</h2> Registrieren Sie sich bitte auf <a href="http://www.semtoo.com" target="_blank">SEMTOO</a>. Nach erfolgreicher Registrierung finden Sie Ihren API-Key in eingeloggtem Zustand im <a href="https://www.semtoo.com/anmeldung.php" target="_blank">Administrationsbereich</a>.';
	  return;
	} elseif (count($this->account) == 0) {
	  echo '<h2>Es ist kein gültiger Account vorhanden!</h2>';
	  return;
	} elseif ($this->account[0] == 0) {
	  echo '<h2>Aufträge zur Texterstellung sind auf maximal '.max($this->account[1],350).' Worte begrenzt.</h2>';
	} else {
	  echo '<h2>Sie haben einen Premium-Account mit monatlichem Volumen! Es stehen noch '.$this->account[1].' Aufträge zur Verfügung.</h2>';
	}

        if ('POST' == $_SERVER['REQUEST_METHOD']) {
	  if (isset($_POST['semtoo_url'])) {

	    $headers = array ('Authorization' => 'Token '.$this->api_key);
	    $url = 'https://www.semtoo.com/api.php';
	    $data = array('action' => 'config', 'id' => $this->api_id, 'url' => $_POST['semtoo_url'], 'tags' => $_POST['semtoo_keywords'], 'anzahl' => $_POST['semtoo_anzahl']);
	    $response = wp_remote_post( $url, array ('method' => 'POST', 'headers' => $headers, 'body' => $data) );
	    echo "<h3>".$response['body']."</h3>";

	  } else {
	    update_option("semtoo_direct", $_POST["semtoo_direct"]);
	  }
        }
?>

<div class="wrap">
    <h1>Konfiguration</h1>
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
	<h2>Einstellungen ändern</h2>
        <!-- Import as draft -->
        <p>
	    <b>Einstellungen<b> auf <a href="https://www.semtoo.com/anmeldung.php" target="_blank">semtoo.com</a> bearbeiten.
        </p>
        <p>
            <label>
                <input name="semtoo_direct" type="checkbox" <?php if (get_option( 'semtoo_direct') == "1") { echo 'checked="checked"'; } ?> value="1" /> <?php _e('Blogeinträge direkt veröffentlichen', 'semtoo') ?>
            </label>
        </p>
        <p class="submit">
            <input type="submit" class="button" name="submit" value="<?php _e('Ok', 'semtoo') ?>" />
        </p>
    </form>
    <hr>
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
	<input type="hidden" name="semtoo_url" value="<?php echo $_SERVER['HTTP_HOST']; ?>">
	<h2>Texterstellung automatisieren</h2>
        <p>Mit diesen Einstellungen können Sie eine automatisierte Texterstellung für Ihre Webseite konfigurieren. SEMTOO verfasst in regelmäßigen Abständen (je nach Art Ihres Accounts x pro Monat) passende Texte für Ihre Seite. Die Themen dafür werden selbständig gesucht. Dazu muss Ihre Webseite thematisch analysiert werden. Geben Sie daher bitte die Keywords ein, die Ihre Seite kategorisieren (kommagetrennt), z.B. "Reisen, Reiseblog, Urlaub" für Blogs, die sich mit dem Thema Reisen beschäftigen, sowie die Anzahl der Texte, die pro Monat erstellt werden sollen. Voraussetzung ist ein passender Account auf www.semtoo.com</p>
<?php
	if ($this->account[0] == 0) {
?>
	  <br><b>Sie haben keinen Monatsaccount: </b><p>Voraussetzung ist ein kostenpflichtiger Account mit monatlichem Volumen. Informationen dazu finden Sie auf <a href="http://www.semtoo.com/preise.html" target="_blank">semtoo.com</a></p>
<?php
	} else {
?>
        <p>
            <label>
                 <?php _e('Keywords dieses Blogs:', 'semtoo') ?> <input name="semtoo_keywords" type="text" value="" />
            </label>
	</p>
        <p>
            <label>
                <?php _e('Anzahl Texte pro Monat:', 'semtoo') ?> <input name="semtoo_anzahl" type="text" value="" />
            </label>
	</p>
<?php
	}
?>
        <p class="submit">
            <input type="submit" class="button" name="submit" value="<?php _e('Ändern', 'semtoo') ?>" />
        </p>
    </form>
</div><!-- end wrap -->

<?php

    }

    function mnew() {

	if (!$this->api_key) {
	  echo '<h2>Es ist noch kein API-Key vorhanden!</h2> Registrieren Sie sich bitte auf <a href="http://www.semtoo.com" target="_blank">SEMTOO</a>. Nach erfolgreicher Registrierung finden Sie Ihren API-Key in eingeloggtem Zustand im <a href="https://www.semtoo.com/anmeldung.php" target="_blank">Administrationsbereich</a>.';
	  return;
	} elseif (count($this->account) == 0) {
	  echo '<h2>Es ist kein gültiger Account vorhanden!</h2>';
	  return;
	} elseif ($this->account[0] == 0) {
	  echo '<h2>Aufträge zur Texterstellung sind auf maximal '.max($this->account[1],350).' Worte begrenzt.</h2>';
	} else {
	  if ($this->account[1] > 0) echo '<h2>Sie haben einen Premium-Account mit monatlichem Volumen! Es stehen noch '.$this->account[1].' Aufträge zur Verfügung.</h2>';
	  else echo '<h2>Sie haben einen Premium-Account mit monatlichem Volumen, allerdings ist Ihr Volumen aufgebraucht! Aufträge zur Texterstellung sind daher auf maximal '.max($this->account[1],350).' Worte begrenzt.</h2>';
	}

        if ('POST' == $_SERVER['REQUEST_METHOD']) {

	    $headers = array ('Authorization' => 'Token '.$this->api_key);
	    $url = 'https://www.semtoo.com/api.php';
	    $data = array('action' => 'text', 'id' => $this->api_id, 'begriff' => $_POST['semtoo_begriff'], 'webanz' => $_POST['semtoo_webanz'], 'maxanz' => $_POST['semtoo_maxanz'], 'theme' => $_POST['semtoo_theme'], 'methode' => 'voll');
	    $response = wp_remote_post( $url, array ('method' => 'POST', 'headers' => $headers, 'body' => $data) );
	    echo "<h3>".$response['body']."</h3>";
        }
?>

<div class="wrap">
    <h1>Einzelnen Text erstellen</h1>
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
        <p></p>

        <p>
            <label>
                 <?php _e('Suchbegriff:', 'semtoo') ?> <br><input name="semtoo_begriff" type="text" value="" />
            </label>
	</p>
        <p>
            <label>
                <?php _e('Anzahl Worte:', 'semtoo') ?> <br><input name="semtoo_maxanz" type="text" value="" />
            </label>
	</p>
        <p>
            <label>
                <?php _e('Anzahl Webseiten:', 'semtoo') ?> <br><input name="semtoo_webanz" type="text" value="" />
            </label>
	</p>
        <p>
            <label>
                <?php _e('Suchart:', 'semtoo') ?>
		<br><select name="semtoo_theme">
		  <option value="standard">Normale Suche</option>
		  <option value="news">News-Suche</option>
		</select>
            </label>
	</p>
        <p><b>Hinweis: </b>Die Anzahl Worte wird automatisch auf 350 begrenzt, sollten Sie einen Standard-Account besitzen oder Ihr Volumen leer sein.</p>
        <p>Weitere Optionen und Erklärungen stehen in Ihrem SEMTOO-Account im Adminbereich auf <a href="https://www.semtoo.com/anmeldung.php" target="_blank">semtoo.com</a> zur Verfügung.</p>
        <p class="submit">
            <input type="submit" class="button" name="submit" value="<?php _e('Auftrag abschicken', 'semtoo') ?>" />
        </p>
    </form>
</div><!-- end wrap -->

<?php

    }

    function mcatch() {

	if (!$this->api_key) {
	  echo '<h2>Es ist noch kein API-Key vorhanden!</h2> Registrieren Sie sich bitte auf <a href="http://www.semtoo.com" target="_blank">SEMTOO</a>. Nach erfolgreicher Registrierung finden Sie Ihren API-Key in eingeloggtem Zustand im <a href="https://www.semtoo.com/anmeldung.php" target="_blank">Administrationsbereich</a>.';
	} else {

	  $headers = array ('Authorization' => 'Token '.$this->api_key);
	  $url = 'https://www.semtoo.com/api.php?action=text&id='.$this->api_id;
	  $response = wp_remote_get( $url, array ('method' => 'GET', 'headers' => $headers) );

	  if ($response['body'] == "XXX") {
?>
<h2>Es ist ein Fehler aufgetreten! Ihre Zugangsdaten sind nicht korrekt.</h2>
<?php
	  } else {
	    $acc = explode ( ";" , $response['body']);
	    $postarr['id'] = 0;
	    $postarr['post_title'] = $acc[0];
	    $postarr['post_content'] = $acc[1];
	    $new_id = wp_insert_post($postarr);
	    $new_id = wp_insert_post($postarr);
	    if ($new_id) {
?>
<h2>Ihr neuer Post wurde eingetragen.</h2>
<?php
	      echo "<a href=\"".admin_url()."post.php?post=$new_id&action=edit\"><p>Zum Post</a></p>";
	    } else {
?>
<h2>Es wurde kein neuer Beitrag gefunden.</h2>
<?php
	    }
	  }
	}
    }

    function form() {
      if (!$this->api_key) {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
	  if ($_POST["semtoo_key_id"] == "" || $_POST["semtoo_api_key"] == "") {
?>

<div class="wrap"><b>Es ist ein Fehler aufgetreten. Bitte geben Sie für jedes Feld einen Wert ein!</b></div>

<?php
	  } else {
	    update_option("semtoo_key_id", $_POST["semtoo_key_id"]);
	    update_option("semtoo_api_key", $_POST["semtoo_api_key"]);
?>

<div class="wrap"><b>Ihre Daten wurden erfolgreich eingetragen!</b></div>

<?php
	   return;
	  }
        }
?>

<div class="wrap">
    <h2>API-Key</h2>
    <p>Bitte registrieren Sie sich auf <a href="http://www.semtoo.com/anmeldung.php" target="_blank">SEMTOO</a>! Loggen Sie sich dort danach bitte mir Ihren Daten ein. Im Adminbereich finden Sie die ID und den Api-Key.</p>
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
        <p>
            <label for="semtoo_key_id"><?php _e('ID:', 'semtoo') ?></label><br />
            <input name="semtoo_key_id" id="semtoo_key_id" type="text" value="" />
        </p>
        <p>
            <label for="semtoo_api_key"><?php _e('API-Key:', 'semtoo') ?></label><br />
            <input name="semtoo_api_key" id="semtoo_api_key" type="text" value="" />
        </p>
        <p class="submit">
            <input type="submit" class="button" name="submit" value="<?php _e('OK', 'semtoo') ?>" />
        </p>
    </form>
</div><!-- end wrap -->

<?php

      } else {
?>

<h2>Ein API-Key ist vorhanden!</h2><!-- end wrap -->

<?php
      }
    }
}

function admin_menu() {
    require_once ABSPATH . '/wp-admin/admin.php';
    $plugin = new Semtoo;

    add_menu_page('Semtoo', 'Semtoo', 10, __FILE__, array($plugin, 'form'));
    add_submenu_page(__FILE__, 'Einzelnen Beitrag erstellen', 'Einzelnen Beitrag erstellen', 10, 'neu', array($plugin, 'mnew'));
    add_submenu_page(__FILE__, 'Neue Beiträge', 'Neue Beiträge', 10, 'beitraege', array($plugin, 'mcatch'));
    add_submenu_page(__FILE__, 'Konfiguration', 'Konfiguration', 10, 'konfiguration', array($plugin, 'configure'));
}

add_action('admin_menu', 'admin_menu');
?>
