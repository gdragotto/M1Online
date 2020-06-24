#import "AppVersion.h"
@implementation AppVersion

@synthesize callbackIDz;

-(void)getVersionNumber:(CDVInvokedUrlCommand*)command
{
    self.callbackIDz = command.callbackId;
    NSString * appVersionString = [[NSBundle mainBundle] objectForInfoDictionaryKey:@"CFBundleShortVersionString"];
	
	[self successWithMessage: appVersionString toID: self.callbackIDz];
}
-(void)successWithMessage:(NSString *)message toID:(NSString *)callbackIDs
{
    CDVPluginResult *commandResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_OK messageAsString:message];
    [self.commandDelegate sendPluginResult:commandResult callbackId:callbackIDs];
}
@end