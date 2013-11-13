<?php
if(array_key_exists('header', $this->data)) {
	if($this->getTag($this->data['header']) !== NULL) {
		$this->data['header'] = $this->t($this->data['header']);
	}
}

$this->includeAtTemplateBase('includes/header.php');
?>

<h2><?php if (isset($this->data['header'])) { echo($this->data['header']); } else { echo($this->t('{status:some_error_occurred}')); } ?></h2>

<p><?php echo($this->t('{status:intro}')); ?></p>

<?php
if (isset($this->data['remaining'])) {
	echo('<p>' . $this->t('{status:validfor}', array('%SECONDS%' => $this->data['remaining'])) . '</p>');
}

if(isset($this->data['sessionsize'])) {
	echo('<p>' . $this->t('{status:sessionsize}', array('%SIZE%' => $this->data['sessionsize'])) . '</p>');
}
?>

<h2><?php echo($this->t('{status:attributes_header}')); ?></h2>

<?php
// consent style listing start
$attributes = $this->data['attributes'];

function present_list($attr) {
	if (is_array($attr) && count($attr) > 1) {
		$str = '<ul>';
		foreach ($attr as $value) {
			$str .= '<li>' . htmlspecialchars($attr) . '</li>';
		}
		$str .= '</ul>';
		return $str;
	} else {
		return htmlspecialchars($attr[0]);
	}
}

function present_assoc($attr) {
	if (is_array($attr)) {
		
		$str = '<dl>';
		foreach ($attr AS $key => $value) {
			$str .= "\n" . '<dt>' . htmlspecialchars($key) . '</dt><dd>' . present_list($value) . '</dd>';
		}
		$str .= '</dl>';
		return $str;
	} else {
		return htmlspecialchars($attr);
	}
}

function present_attributes($t, $attributes, $nameParent) {
	$alternate = array('odd', 'even'); $i = 0;
	
	$parentStr = (strlen($nameParent) > 0)? strtolower($nameParent) . '_': '';
	$str = (strlen($nameParent) > 0)? '<table class="attributes" summary="attribute overview">':
		'<table id="table_with_attributes"  class="attributes" summary="attribute overview">';

	foreach ($attributes as $name => $value) {
	
		$nameraw = $name;
		$name = $t->getAttributeTranslation($parentStr . $nameraw);

		if (preg_match('/^child_/', $nameraw)) {
			$parentName = preg_replace('/^child_/', '', $nameraw);
			foreach($value AS $child) {
				$str .= '<tr class="odd"><td colspan="2" style="padding: 2em">' . present_attributes($t, $child, $parentName) . '</td></tr>';
			}
		} else {	
			if (sizeof($value) > 1) {
				$str .= '<tr class="' . $alternate[($i++ % 2)] . '"><td class="attrname">' . htmlspecialchars($name) . '</td><td class="attrvalue"><ul>';
				foreach ($value AS $listitem) {
					if ($nameraw === 'jpegPhoto') {
						$str .= '<li><img src="data:image/jpeg;base64,' . htmlspecialchars($listitem) . '" /></li>';
					} else {
						$str .= '<li>' . present_assoc($listitem) . '</li>';
					}
				}
				$str .= '</ul></td></tr>';
			} elseif(isset($value[0])) {
				$str .= '<tr class="' . $alternate[($i++ % 2)] . '"><td class="attrname">' . htmlspecialchars($name) . '</td>';
				if ($nameraw === 'jpegPhoto') {
					$str .= '<td class="attrvalue"><img src="data:image/jpeg;base64,' . htmlspecialchars($value[0]) . '" /></td></tr>';
				} else {
					$str .= '<td class="attrvalue">' . htmlspecialchars($value[0]) . '</td></tr>';
				}
			}
		}
		$str .= "\n";
	}
	$str .= '</table>';
	return $str;
}
	
echo(present_attributes($this, $attributes, ''));
// consent style listing end

if (isset($this->data['logout'])) {
	echo('<h2>' . $this->t('{status:logout}') . '</h2>');
	echo('<p>' . $this->data['logout'] . '</p>');
}

if (isset($this->data['logouturl'])) {
	echo('<h2>' . $this->t('{status:logout}') . '</h2>');
	echo('<p>[ <a href="' . htmlspecialchars($this->data['logouturl']) . '">' . $this->t('{status:logout}') . '</a> ]</p>');
}
?>

	<h2><?php echo $this->t('{core:frontpage:about_header}'); ?></h2>
	<p><?php echo $this->t('{core:frontpage:about_text}'); ?></p>
	
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>