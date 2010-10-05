package  {
	
	import flash.display.*;
    import flash.events.*;
    import flash.net.*;
	import flash.external.*;
	import flash.text.*
	
	public class UploadSwfDialog extends Sprite {
		// Cause SWFUpload to start as soon as the movie starts
		public static function main():void
		{
			var UploadSwfDialog = new UploadSwfDialog();
		}
		
		protected var doNothing:Function = function ():void { };
		
		private function Debug(msg:String):void {
			try {
				if (this.debugEnabled) {
					var lines:Array = msg.split("\n");
					for (var i:Number=0; i < lines.length; i++) {
						lines[i] = "UPLOAD SWF DIDALOG DEBUG: " + lines[i];
					}
					ExternalInterface.call(this.externalClass+'.Debug',lines.join("\n"));
				}
			} catch (ex:Error) {
				// pretend nothing happened
				trace(ex);
			}
		}
		
		public function UploadSwfDialog() {
			var counter:Number = 0;
			root.addEventListener(Event.ENTER_FRAME, function ():void { if (++counter > 100) counter = 0; });
	
			this.SetupFileReferenceList();
			this.SetupFileArray();
			this.SetupButtonLoader();
			
			this.SetupExternal();
		}
		
		protected function SetupExternal (){
			this.SetupExternalVariables();
			this.SetupExternalCalls();
			this.SetupExternalCallBacks();
		}
		
		protected function SetupExternalVariables (){
			try {
				this.fileTypes = root.loaderInfo.parameters.fileTypes;
				this.fileTypesDescription = root.loaderInfo.parameters.fileTypesDescription;
			} catch (ex:Object) {
				this.fileTypes = "*.*";
				this.fileTypesDescription = "All Files";
			}
			if (!this.fileTypes || !this.fileTypesDescription){
				this.fileTypes = "*.*";
				this.fileTypesDescription = "All Files";
			}
			this.fileTypesDescription = this.fileTypesDescription + " (" + this.fileTypes + ")";
			try {
				this.externalClass = root.loaderInfo.parameters.externalClass;
			} catch (ex:Object) {
				this.externalClass = "Ext.ux.UploadDialog.BrowseButton";
			}
			if (!this.externalClass){
				this.externalClass = "Ext.ux.UploadDialog.BrowseButton";
			}
			try {
				this.debugEnabled = root.loaderInfo.parameters.debugEnabled == "true" ? true : false;
			} catch (ex:Object) {
				this.debugEnabled = false;
			}
		
			try {
				this.SetButtonDimensions(Number(root.loaderInfo.parameters.buttonWidth), Number(root.loaderInfo.parameters.buttonHeight));
			} catch (ex:Object) {
				this.SetButtonDimensions(0, 0);
			}

			try {
				this.SetButtonImageURL(String(root.loaderInfo.parameters.buttonImageURL));
			} catch (ex:Object) {
				this.SetButtonImageURL("");
			}
			
			try {
				this.SetButtonText(String(root.loaderInfo.parameters.buttonText));
			} catch (ex:Object) {
				this.SetButtonText("");
			}
			
			try {
				this.SetButtonTextPadding(Number(root.loaderInfo.parameters.buttonTextLeftPadding), Number(root.loaderInfo.parameters.buttonTextTopPadding));
			} catch (ex:Object) {
				this.SetButtonTextPadding(0, 0);
			}

			try {
				this.SetButtonTextStyle(String(root.loaderInfo.parameters.buttonTextStyle));
			} catch (ex:Object) {
				this.SetButtonTextStyle("");
			}

			try {
				this.SetButtonAction(Number(root.loaderInfo.parameters.buttonAction));
			} catch (ex:Object) {
				this.SetButtonAction(this.BUTTON_ACTION_SELECT_FILES);
			}
			
			try {
				this.SetButtonDisabled(root.loaderInfo.parameters.buttonDisabled == "true" ? true : false);
			} catch (ex:Object) {
				this.SetButtonDisabled(Boolean(false));
			}
			
			try {
				this.SetButtonCursor(Number(root.loaderInfo.parameters.buttonCursor));
			} catch (ex:Object) {
				this.SetButtonCursor(this.BUTTON_CURSOR_ARROW);
			}
		}
		
		protected function SetupExternalCalls (){
			this.callsArray = new Array();
			
			this.callsArray['FileSelected'] = this.externalClass+'.FileSelected';
		}
		
		protected function SetupExternalCallBacks (){
			this.callBacksArray = new Array();
		}
		
		protected function SetupFileReferenceList (){
			this.fileBrowserMany = new flash.net.FileReferenceList();
			this.fileBrowserMany.addEventListener(flash.events.Event.SELECT, this.Select_Many_Handler);
            this.fileBrowserMany.addEventListener(flash.events.Event.CANCEL, this.DialogCancelled_Handler);
		}
		
		protected function SetupFileArray (){
			this.fileArray = new Array();
		}
		
		protected function SetupButtonLoader (){
			this.buttonLoader = new Loader();
			this.buttonLoader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, this.doNothing );
			this.buttonLoader.contentLoaderInfo.addEventListener(HTTPStatusEvent.HTTP_STATUS, this.doNothing );
			this.stage.addChild(this.buttonLoader);

			var self:UploadSwfDialog = this;
			
			this.stage.addEventListener(MouseEvent.CLICK, function (event:MouseEvent):void {
				self.UpdateButtonState();
				self.ButtonClickHandler(event);
			});
			this.stage.addEventListener(MouseEvent.MOUSE_DOWN, function (event:MouseEvent):void {
				self.buttonStateMouseDown = true;
				self.UpdateButtonState();
			});
			this.stage.addEventListener(MouseEvent.MOUSE_UP, function (event:MouseEvent):void {
				self.buttonStateMouseDown = false;
				self.UpdateButtonState();
			});
			this.stage.addEventListener(MouseEvent.MOUSE_OVER, function (event:MouseEvent):void {
				self.buttonStateMouseDown = event.buttonDown;
				self.buttonStateOver = true;
				self.UpdateButtonState();
			});
			this.stage.addEventListener(MouseEvent.MOUSE_OUT, function (event:MouseEvent):void {
				self.buttonStateMouseDown = false;
				self.buttonStateOver = false;
				self.UpdateButtonState();
			});
			
			this.stage.addEventListener(Event.MOUSE_LEAVE, function (event:Event):void {
				self.buttonStateMouseDown = false;
				self.buttonStateOver = false;
				self.UpdateButtonState();
			});
			
			this.SetupButtonTextField();
			this.SetupButtonCursorSprite();
		}
		
		protected function SetupButtonTextField (){
			this.buttonTextField = new TextField();
			this.buttonTextField.type = TextFieldType.DYNAMIC;
			this.buttonTextField.antiAliasType = AntiAliasType.ADVANCED;
			this.buttonTextField.autoSize = TextFieldAutoSize.NONE;
			this.buttonTextField.cacheAsBitmap = true;
			this.buttonTextField.multiline = true;
			this.buttonTextField.wordWrap = false;
			this.buttonTextField.tabEnabled = false;
			this.buttonTextField.background = false;
			this.buttonTextField.border = false;
			this.buttonTextField.selectable = false;
			this.buttonTextField.condenseWhite = true;
			
			this.stage.addChild(this.buttonTextField);
		}
		
		protected function SetupButtonCursorSprite (){
			this.buttonCursorSprite = new Sprite();
			this.buttonCursorSprite.graphics.beginFill(0xFFFFFF, 0);
			this.buttonCursorSprite.graphics.drawRect(0, 0, 1, 1);
			this.buttonCursorSprite.graphics.endFill();
			this.buttonCursorSprite.buttonMode = true;
			this.buttonCursorSprite.x = 0;
			this.buttonCursorSprite.y = 0;
			this.buttonCursorSprite.addEventListener(MouseEvent.CLICK, this.doNothing);
			
			this.stage.addChild(this.buttonCursorSprite);
		}
		
		/* *************************************************************
			Button Handling Functions
		*************************************************************** */
		
		private function SetButtonImageURL(button_image_url:String):void {
			this.buttonImageURL = button_image_url;

			try {
				if (this.buttonImageURL !== null && this.buttonImageURL !== "") {
					this.buttonLoader.load(new URLRequest(this.buttonImageURL));
				}
			} catch (ex:Object) {
			}
		}
		
		private function ButtonClickHandler(e:MouseEvent):void {
			if (!this.buttonStateDisabled) {
				this.SelectFiles();
			}
		}
		
		private function SelectFiles():void {
			var allowed_file_types:String = "*.*";
			var allowed_file_types_description:String = "All Files";
			
			if (this.fileTypes.length > 0) allowed_file_types = this.fileTypes;
			if (this.fileTypesDescription.length > 0)  allowed_file_types_description = this.fileTypesDescription;
			
			this.Debug("Event: fileDialogStart : Browsing files. Multi Select. Allowed file types: " + allowed_file_types);

			try {
				this.fileBrowserMany.browse([new FileFilter(allowed_file_types_description, allowed_file_types)]);
			} catch (ex:Error) {
				this.Debug("Exception: " + ex.toString());
			}
		}
		
		private function UpdateButtonState():void {
			var xOffset:Number = 0;
			var yOffset:Number = 0;
			
			this.buttonLoader.x = xOffset;
			this.buttonLoader.y = yOffset;
			
			if (this.buttonStateDisabled) {
				this.buttonLoader.y = this.buttonHeight * -3 + yOffset;
			}
			else if (this.buttonStateMouseDown) {
				this.buttonLoader.y = this.buttonHeight * -2 + yOffset;
			}
			else if (this.buttonStateOver) {
				this.buttonLoader.y = this.buttonHeight * -1 + yOffset;
			}
			else {
				this.buttonLoader.y = -yOffset;
			}
		}
		
		private function SetButtonDimensions(width:Number = -1, height:Number = -1):void {
			if (width >= 0) {
				this.buttonWidth = width;
			}
			if (height >= 0) {
				this.buttonHeight = height;
			}
			
			this.buttonTextField.width = this.buttonWidth;
			this.buttonTextField.height = this.buttonHeight;
			this.buttonCursorSprite.width = this.buttonWidth;
			this.buttonCursorSprite.height = this.buttonHeight;
			
			this.UpdateButtonState();
		}
		
		private function SetButtonText(button_text:String):void {
			this.buttonText = button_text;
			
			this.SetButtonTextStyle(this.buttonTextStyle);
		}
		
		private function SetButtonTextStyle(button_text_style:String):void {
			this.buttonTextStyle = button_text_style;
			
			var style:StyleSheet = new StyleSheet();
			style.parseCSS(this.buttonTextStyle);
			this.buttonTextField.styleSheet = style;
			this.buttonTextField.htmlText = this.buttonText;
		}

		private function SetButtonTextPadding(left:Number, top:Number):void {
				this.buttonTextField.x = this.buttonTextLeftPadding = left;
				this.buttonTextField.y = this.buttonTextTopPadding = top;
		}
		
		private function SetButtonDisabled(disabled:Boolean):void {
			this.buttonStateDisabled = disabled;
			this.UpdateButtonState();
		}
		
		private function SetButtonAction(button_action:Number):void {
			this.buttonAction = button_action;
		}
		
		private function SetButtonCursor(button_cursor:Number):void {
			this.buttonCursor = button_cursor;
			
			this.buttonCursorSprite.useHandCursor = (button_cursor === this.BUTTON_CURSOR_HAND);
		}
		
		/* *****************************************
		* FileReference Event Handlers
		* *************************************** */
		
		private function Select_Many_Handler(arg1:flash.events.Event):void {
            //this.Select_Handler(this.fileBrowserMany.fileList);
			this.Debug("Select Handler: Received the files selected from the dialog. Processing the file list...");
			for (var i:Number = 0; i < this.fileBrowserMany.fileList.length; i++) {
				file_item = this.fileBrowserMany.fileList[i];
				this.fileArray.push(file_item);
				this.Debug("Event: fileQueued : File ID: " + file_item.name);
				ExternalInterface.call(this.callsArray['FileSelected'], this.FileToJavaScriptObject(file_item));
			}
            return;
        }
		
		private function DialogCancelled_Handler(arg1:flash.events.Event):void {
            this.Debug("Event: fileDialogComplete: File Dialog window cancelled.");
            //ExternalCall.FileDialogComplete(this.fileDialogComplete_Callback, 0, 0, this.queued_uploads);
            return;
        }
		
		private function FileToJavaScriptObject(fileReference: FileReference):Object{
			return {};
		}
		
		// Button Actions
		private var BUTTON_ACTION_SELECT_FILE:Number                = -100;
		private var BUTTON_ACTION_SELECT_FILES:Number               = -110;
		private var BUTTON_ACTION_START_UPLOAD:Number               = -120;
		private var BUTTON_CURSOR_ARROW:Number						= -1;
		private var BUTTON_CURSOR_HAND:Number						= -2;
		
		
		private var fileBrowserMany:FileReferenceList;
		private var fileArray:Array;
		private var callsArray:Array;
		private var callBacksArray:Array;
		
		private var buttonLoader:Loader;		
		private var buttonStateOver:Boolean;
		private var buttonStateMouseDown:Boolean;
		private var buttonStateDisabled:Boolean;
		private var buttonTextField:TextField;
		private var buttonCursorSprite:Sprite;
		private var buttonImageURL:String;
		private var buttonText:String;
		private var buttonTextStyle:String;
		private var buttonTextTopPadding:Number;
		private var buttonTextLeftPadding:Number;
		private var buttonAction:Number;
		private var buttonCursor:Number;
		
		//External loadded variables
		private var buttonWidth:Number = 100;
		private var buttonHeight:Number = 50;
		
		private var fileTypes:String;
		private var fileTypesDescription:String;
		private var externalClass:String;
		
		private var debugEnabled:Boolean = false;
		
		/*/-------------------unnown-------------

		private var movieName:String;
		private var uploadURL:String;
		private var filePostName:String;
		private var uploadPostObject:Object;
		private var fileSizeLimit:Number;
		private var fileUploadLimit:Number = 0;
		private var fileQueueLimit:Number = 0;
		private var useQueryString:Boolean = false;
		private var requeueOnError:Boolean = false;
		private var httpSuccess:Array = [];
		private var assumeSuccessTimeout:Number = 0;
		*/
	}
	
}