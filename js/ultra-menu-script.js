jQuery(document).ready( function($) {
   function ct_media_upload(button_class) {
	 var _custom_media = true,
	 _orig_send_attachment = wp.media.editor.send.attachment;
	 $('body').on('click', button_class, function(e) {
	   var button_id = '#'+$(this).attr('id');
	   var field_id = $(this).data('itemid');
	   var send_attachment_bkp = wp.media.editor.send.attachment;
	   var button = $(button_id);
	   _custom_media = true;
	   wp.media.editor.send.attachment = function(props, attachment){
		 if ( _custom_media ) {
		   $('#'+field_id).val(attachment.id);
		   $('#ultra_menu_image_wrapper_'+field_id).html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
		   $('#ultra_menu_image_wrapper_'+field_id+' .custom_media_image').attr('src',attachment.sizes.thumbnail.url).css('display','block');
		 } else {
		   return _orig_send_attachment.apply( button_id, [props, attachment] );
		 }
		}
	 wp.media.editor.open(button);
	 return false;
   });
 }
 ct_media_upload('.ultra_menu_upload.button'); 
 $('body').on('click','.ultra_menu_remove',function(){
   var field_id = $(this).data('itemid');
   $('#'+field_id).val('');
   $('#ultra_menu_image_wrapper_'+field_id).html('');
 });
});