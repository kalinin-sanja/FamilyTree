<?php
//print '<div class="pad2"></div>';
echo '</div>'; // End of div: Content.
echo '</div>'; // End of div: Silverline.

if ($humo_option["text_footer"]) echo "<br>\n".$humo_option["text_footer"];

if (!CMS_SPECIFIC){
	?>
	<!-- YOU CAN ADD YOUR OWN HTML CODE IN THE BLOCK BELOW -->



	<!-- END OF OWN HTML CODE BLOCK -->
	<?php
	echo "</body>\n";
	echo "</html>";
}
?>