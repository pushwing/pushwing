var FeaturesDemoHandlers = {
	swfUploadLoaded : function () {
		FeaturesDemo.start(this);  // This refers to the SWFObject because SWFUpload calls this with .apply(this).
	},
	fileDialogStart : function () {
		try {
			FeaturesDemo.selEventsQueue.options[FeaturesDemo.selEventsQueue.options.length] = new Option("File Dialog Start", "");
		} catch (ex) {
			this.debug(ex);
		}
	},

	fileQueued : function (file) {
		try {
			var queueString = file.id + ":  0%:" + file.name;
			FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.options.length] = new Option(queueString, file.id);
			FeaturesDemo.selEventsQueue.options[FeaturesDemo.selEventsQueue.options.length] = new Option("File Queued: " + file.id, "");
		} catch (ex) {
			this.debug(ex);
		}
	},

	fileQueueError : function (file, errorCode, message) {
		try {
			var errorName = "";
			switch (errorCode) {
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
				errorName = "QUEUE LIMIT EXCEEDED";
				break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				errorName = "FILE EXCEEDS SIZE LIMIT";
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				errorName = "ZERO BYTE FILE";
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				errorName = "INVALID FILE TYPE";
				break;
			default:
				errorName = "UNKNOWN";
				break;
			}

			var errorString = errorName + ":File ID: " + (typeof(file) === "object" && file !== null ? file.id : "na") + ":" + message;
			FeaturesDemo.selEventsQueue.options[FeaturesDemo.selEventsQueue.options.length] = new Option("File Queue Error: " + errorString, "");

		} catch (ex) {
			this.debug(ex);
		}
	},
	
	fileDialogComplete : function (numFilesSelected, numFilesQueued) {
		try {
			FeaturesDemo.selEventsQueue.options[FeaturesDemo.selEventsQueue.options.length] = new Option("File Dialog Complete: " + numFilesSelected + ", " + numFilesQueued, "");
		} catch (ex) {
			this.debug(ex);
		}
	},
	
	uploadStart : function (file) {
		try {
			FeaturesDemo.selEventsFile.options[FeaturesDemo.selEventsFile.options.length] = new Option("File Start: " + file.id, "");
		} catch (ex) {
			this.debug(ex);
		}

		return true;
	},

	uploadProgress : function (file, bytesLoaded, totalBytes) {

		try {
			var percent = Math.ceil((bytesLoaded / file.size) * 100);
			if (percent < 10) {
				percent = "  " + percent;
			} else if (percent < 100) {
				percent = " " + percent;
			}

			FeaturesDemo.selQueue.value = file.id;
			var queueString = file.id + ":" + percent + "%:" + file.name;
			FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.selectedIndex].text = queueString;


			FeaturesDemo.selEventsFile.options[FeaturesDemo.selEventsFile.options.length] = new Option("Upload Progress: " + bytesLoaded, "");
		} catch (ex) {
			this.debug(ex);
		}
	},

	uploadSuccess : function (file, serverData, receivedResponse) {
		try {
			var queueString = file.id + ":Done:" + file.name;
			FeaturesDemo.selQueue.value = file.id;
			FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.selectedIndex].text = queueString;

			FeaturesDemo.selEventsFile.options[FeaturesDemo.selEventsFile.options.length] = new Option("Upload Success: " + file.id, "");

			if (receivedResponse) {
				FeaturesDemo.divServerData.innerHTML = typeof(serverData) === "undefined" ? "" : serverData; //.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/\t/g, "    ").replace(/  /g, " &nbsp;");
			} else {
				FeaturesDemo.divServerData.innerHTML = "assume_success_timeout setting timed out before a response was received from the server";
			}
		} catch (ex) {
			this.debug(ex);
		}
	},

	uploadError : function (file, errorCode, message) {
		FeaturesDemo.divServerData.innerHTML = "";
		try {
			var errorName = "";
			switch (errorCode) {
			case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
				FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.selectedIndex].text = file.id + ":HTTP:" + file.name;
				errorName = "HTTP ERROR";
				break;
			case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
				errorName = "MISSING UPLOAD URL";
				break;
			case SWFUpload.UPLOAD_ERROR.IO_ERROR:
				FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.selectedIndex].text = file.id + ":IO  :" + file.name;
				errorName = "IO ERROR";
				break;
			case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
				FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.selectedIndex].text = file.id + ":SEC :" + file.name;
				errorName = "SECURITY ERROR";
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				errorName = "UPLOAD LIMIT EXCEEDED";
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
				errorName = "UPLOAD FAILED";
				break;
			case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
				errorName = "SPECIFIED FILE ID NOT FOUND";
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
				errorName = "FILE VALIDATION FAILED";
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
				errorName = "FILE CANCELLED";
				
				FeaturesDemo.selQueue.value = file.id;
				FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.selectedIndex].text = file.id + ":----:" + file.name;

				FeaturesDemo.selEventsFile.options[FeaturesDemo.selEventsFile.options.length] = new Option("File Cancelled " + file.id, "");
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				errorName = "FILE STOPPED";
				
				FeaturesDemo.selQueue.value = file.id;
				FeaturesDemo.selQueue.options[FeaturesDemo.selQueue.selectedIndex].text = file.id + ":  0%:" + file.name;

				FeaturesDemo.selEventsFile.options[FeaturesDemo.selEventsFile.options.length] = new Option("File Stopped " + file.id, "");
				break;
			default:
				errorName = "UNKNOWN";
				break;
			}

			var errorString = errorName + ":File ID: " + (typeof(file) === "object" && file !== null ? file.id : "na") + ":" + message;
			FeaturesDemo.selEventsFile.options[FeaturesDemo.selEventsFile.options.length] = new Option(errorString, "");

		} catch (ex) {
			this.debug(ex);
		}
	},
	
	uploadComplete : function (file) {
		try {
			FeaturesDemo.selEventsFile.options[FeaturesDemo.selEventsFile.options.length] = new Option("Upload Complete: " + file.id, "");
		} catch (ex) {
			this.debug(ex);
		}
	},
	
	// This custom debug method sends all debug messages to the Firebug console.  If debug is enabled it then sends the debug messages
	// to the built in debug console.  Only JavaScript message are sent to the Firebug console when debug is disabled (SWFUpload won't send the messages
	// when debug is disabled).
	debug : function (message) {
		try {
			if (window.console && typeof(window.console.error) === "function" && typeof(window.console.log) === "function") {
				if (typeof(message) === "object" && typeof(message.name) === "string" && typeof(message.message) === "string") {
					window.console.error(message);
				} else {
					window.console.log(message);
				}
			}
		} catch (ex) {
		}
		try {
			if (this.settings.debug) {
				this.debugMessage(message);
			}
		} catch (ex1) {
		}
	}
};



//파일핸들러
function fileQueued(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("Pending...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}

}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("File is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("Cannot upload Zero Byte files.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("Invalid File Type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("Unhandled Error");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesSelected > 0) {
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}
		
		/* I want auto start the upload and I can do that here */
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function uploadStart(file) {
	try {
		/* I don't want to do any file validation or anything,  I'll just update the UI and
		return true to indicate that the upload should start.
		It's important to update the UI here because in Linux no uploadProgress events are called. The best
		we can do is say we are uploading.
		 */
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("Uploading...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}
	
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("Uploading...");
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus("Complete.");
		progress.toggleCancel(false);
        
        _mockdata[_mockdata.length] = eval('('+serverData+')');
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("Upload Error: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("Upload Failed.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("Server (IO) Error");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("Upload limit exceeded.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("Failed Validation.  Upload skipped.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("Cancelled");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Stopped");
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
	if (this.getStats().files_queued === 0) {
		document.getElementById(this.customSettings.cancelButtonId).disabled = true;
	}
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + "개 업로드 완료";
}



