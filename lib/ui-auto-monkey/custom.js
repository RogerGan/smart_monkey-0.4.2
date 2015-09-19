#import "UIAutoMonkey.js"
#import "handler/buttonHandler.js"
#import "handler/wbScrollViewButtonHandler.js"
#import "tuneup/tuneup.js"

// Configure the monkey: use the default configuration but a bit tweaked
monkey = new UIAutoMonkey();
monkey.config.numberOfEvents = 1999; // turn off to make clear that we want minutes
monkey.config.delayBetweenEvents = 0.05;

monkey.config.touchProbability = {
			multipleTaps: 0.05,
			multipleTouches: 0.05,
			longPress: 0.05
		};


//UI Holes handlers
var handlers = [];
handlers.push(new ButtonHandler("Back", 10, true));
// handlers.push(new WBScrollViewButtonHandler("weatherLeftBack", 5, false, 1));
// handlers.push(new ButtonHandler("取消", 3, true));
// handlers.push(new ButtonHandler("CloseX", 3, true));
// handlers.push(new ButtonHandler("XXX", 3, false));

monkey.config.conditionHandlers = handlers;

monkey.config.eventWeights = {
			tap: 200,
			drag: 5,
			flick: 0,
			orientation: 0,
			clickVolumeUp: 0,
			clickVolumeDown: 0,
			lock: 0,
			pinchClose: 0,
			pinchOpen: 0,
			shake: 0,
			tapback: 10,
			backtoMainFrame: 10,
			tapNavigationBar: 20,
			tapMainFrameTabBar: 20
		};

monkey.config.touchProbability = {
			multipleTaps: 0.05,
			multipleTouches: 0.05,
			longPress: 0.05
		};

monkey.config.frame = {
			origin: {x: 0, y: 0},
			//size: {width: 100, height: 50}
			// size: {width: UIATarget.localTarget().rect().size.width, height: UIATarget.localTarget().rect().size.height - 50}
            size: {width: UIATarget.localTarget().rect().size.width, height: UIATarget.localTarget().rect().size.height}
		};// Ignore the UIAStatusBar area, avoid to drag out the notification page. 

//ANR settings
var aFingerprintFunction = function() {
    var mainWindow = UIATarget.localTarget().frontMostApp().mainWindow();
    //if an error occurs log it and make it the fingerprint
    try {
        var aString = mainWindow.elementAccessorDump("tree", true);
        // var aString = mainWindow.logElementTree();
        // var aString = mainWindow.logElementJSON(["name"])
        if (monkey.config.anrSettings.debug) {
            UIALogger.logDebug("fingerprintFunction tree=" + aString);
        }
    }
    catch (e) {
        aString = "fingerprintFunction error:" + e;
        UIALogger.logWarning(aString);
    }
    return aString;
};
monkey.config.anrSettings.fingerprintFunction = false;//false | aFingerprintFunction
monkey.config.anrSettings.eventsBeforeANRDeclared = 18; //throw exception if the fingerprint hasn't changed within this number of events
monkey.config.anrSettings.eventsBetweenSnapshots = 8; //how often (in events) to take a snapshot using the fingerprintFunction 
monkey.config.anrSettings.debug = false;  //log extra info on ANR state changes

function login() {
    var target = UIATarget.localTarget();
    target.delay(5)
    var cell = target.frontMostApp().mainWindow().staticTexts()["Login.LoginLabel"];
    var userguidelogin = target.frontMostApp().mainWindow().buttons()["UserGuide.LoginButton"];
    if (userguidelogin.isValid()) {
        userguidelogin.tap();
    }
    try {
        UIALogger.logStart("Start Login");
        var username = "18819822368";
        var pwd = "ucweb123";
        target.frontMostApp().mainWindow().textFields()["Login.UsernameField"].setValue(username);
        target.frontMostApp().mainWindow().secureTextFields()["Login.PwdField"].setValue(pwd);
        target.frontMostApp().mainWindow().buttons()["Login.LoginButton"].tap();
        UIALogger.logPass("登录成功");
    } catch(e) {
        UIALogger.logMessage("登录过程中出现问题 or 已经登陆好的状态");
    }
}
UIALogger.logMessage(JSON.stringify(UIATarget.localTarget().rect().size));
// login();
// target.frontMostApp().mainWindow().buttons()["MainTab.MessageButton"].tap();
// Release the monkey!
monkey.RELEASE_THE_MONKEY();
UIALogger.logPass("monkey test success");
