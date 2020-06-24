#import <Foundation/Foundation.h>
#import <Cordova/CDVPlugin.h>
#import <QuickLook/QuickLook.h>

@interface AppVersion : CDVPlugin {
   NSString* callbackIDz;
}

@property(nonatomic,copy) NSString* callbackIDz;

// Instance Method
-(void) getVersionNumber:(CDVInvokedUrlCommand*)command;
@end
