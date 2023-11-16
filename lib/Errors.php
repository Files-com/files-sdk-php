<?php

declare(strict_types=1);

namespace Files {

function handleErrorResponse($error) {
  $className = null;

  $response = @$error->getResponse();
  $body = @$response->getBody()->getContents();
  $errorData = json_decode($body);

  if ($errorData === null) {
    throw new ApiException($error->getMessage(), $error->getCode());
  }

  if (is_array($errorData)) {
    $errorData = $errorData[0];
  }

  if ($errorData) {
    if (!@$errorData->type) {
      throw new ApiException($error->getMessage(), $error->getCode());
    }

    $toPascalCase = function($errorPart) {
      return implode('', array_map('\\ucfirst', explode('-', $errorPart)));
    };

    $parts = explode('/', $errorData->type);

    if (count($parts) > 1) {
      list($errorFamily, $errorType) = array_map($toPascalCase, $parts);
      $className = $errorFamily . '\\' . $errorType . 'Exception';
    } else {
      $errorType = $toPascalCase($parts[0]);
      $className = $errorType . 'Exception';
    }
  }

  if (!$className) {
    switch (get_class($error)) {
      case 'GuzzleHttp\\Exception\\TransferException':
        $className = 'ApiTransferException';
        break;

      case 'GuzzleHttp\\Exception\\ConnectException':
        $className = 'ApiConnectException';
        break;

      case 'GuzzleHttp\\Exception\\RequestException':
        $className = 'ApiRequestException';
        break;

      case 'GuzzleHttp\\Exception\\BadResponseException':
        $className = 'ApiBadResponseException';
        break;

      case 'GuzzleHttp\\Exception\\ServerException':
        $className = 'ApiServerException';
        break;

      case 'GuzzleHttp\\Exception\\ClientException':
        $className = 'ApiClientException';
        break;

      case 'GuzzleHttp\\Exception\\TooManyRedirectsException':
        $className = 'ApiTooManyRedirectsException';
        break;
    }
  }

  if ($className) {
    $ExceptionClass = "\\Files\\{$className}";
  } else {
    $ExceptionClass = '\\Files\\ApiException';
  }

  throw new $ExceptionClass($error->getMessage(), $error->getCode());
}

class ApiException extends \Exception {
  public function __construct($message, $code) {
    Logger::error(get_called_class() . ' > ' . $message . ' (code: ' . $code . ')');

    parent::__construct($message, $code);
  }
}

// general errors
class FilesException extends \Exception {}
class ConfigurationException extends FilesException {}
class EmptyPropertyException extends FilesException {}
class InvalidParameterException extends FilesException {}
class MissingParameterException extends FilesException {}
class NotImplementedException extends FilesException {}

// guzzle errors
class ApiTransferException extends ApiException {}
class ApiConnectException extends ApiException {}
class ApiRequestException extends ApiException {}
class ApiBadResponseException extends ApiException {}
class ApiServerException extends ApiException {}
class ApiClientException extends ApiException {}
class ApiTooManyRedirectsException extends ApiException {}

// api error groups
class BadRequestException extends FilesException {}
class NotAuthenticatedException extends FilesException {}
class NotAuthorizedException extends FilesException {}
class NotFoundException extends FilesException {}
class ProcessingFailureException extends FilesException {}
class RateLimitedException extends FilesException {}
class ServiceUnavailableException extends FilesException {}
class SiteConfigurationException extends FilesException {}

} // namespace Files

// grouped api errors

namespace Files\BadRequest {

class AgentUpgradeRequiredException extends \Files\BadRequestException {}
class AttachmentTooLargeException extends \Files\BadRequestException {}
class CannotDownloadDirectoryException extends \Files\BadRequestException {}
class CantMoveWithMultipleLocationsException extends \Files\BadRequestException {}
class DatetimeParseException extends \Files\BadRequestException {}
class DestinationSameException extends \Files\BadRequestException {}
class FolderMustNotBeAFileException extends \Files\BadRequestException {}
class InvalidBodyException extends \Files\BadRequestException {}
class InvalidCursorException extends \Files\BadRequestException {}
class InvalidCursorTypeForSortException extends \Files\BadRequestException {}
class InvalidEtagsException extends \Files\BadRequestException {}
class InvalidFilterAliasCombinationException extends \Files\BadRequestException {}
class InvalidFilterCombinationException extends \Files\BadRequestException {}
class InvalidFilterFieldException extends \Files\BadRequestException {}
class InvalidFilterParamException extends \Files\BadRequestException {}
class InvalidInputEncodingException extends \Files\BadRequestException {}
class InvalidInterfaceException extends \Files\BadRequestException {}
class InvalidOauthProviderException extends \Files\BadRequestException {}
class InvalidPathException extends \Files\BadRequestException {}
class InvalidReturnToUrlException extends \Files\BadRequestException {}
class InvalidUploadOffsetException extends \Files\BadRequestException {}
class InvalidUploadPartGapException extends \Files\BadRequestException {}
class InvalidUploadPartSizeException extends \Files\BadRequestException {}
class MethodNotAllowedException extends \Files\BadRequestException {}
class NoValidInputParamsException extends \Files\BadRequestException {}
class OperationOnNonScimResourceException extends \Files\BadRequestException {}
class PartNumberTooLargeException extends \Files\BadRequestException {}
class ReauthenticationNeededFieldsException extends \Files\BadRequestException {}
class RequestParamPathCannotHaveTrailingWhitespaceException extends \Files\BadRequestException {}
class RequestParamsContainInvalidCharacterException extends \Files\BadRequestException {}
class RequestParamsInvalidException extends \Files\BadRequestException {}
class RequestParamsRequiredException extends \Files\BadRequestException {}
class SearchAllOnChildPathException extends \Files\BadRequestException {}
class UnsupportedCurrencyException extends \Files\BadRequestException {}
class UnsupportedHttpResponseFormatException extends \Files\BadRequestException {}
class UnsupportedMediaTypeException extends \Files\BadRequestException {}
class UserIdInvalidException extends \Files\BadRequestException {}
class UserIdOnUserEndpointException extends \Files\BadRequestException {}
class UserRequiredException extends \Files\BadRequestException {}

} // namespace Files\BadRequest

namespace Files\NotAuthenticated {

class AuthenticationRequiredException extends \Files\NotAuthenticatedException {}
class BundleRegistrationCodeFailedException extends \Files\NotAuthenticatedException {}
class FilesAgentTokenFailedException extends \Files\NotAuthenticatedException {}
class InboxRegistrationCodeFailedException extends \Files\NotAuthenticatedException {}
class InvalidCredentialsException extends \Files\NotAuthenticatedException {}
class InvalidOauthException extends \Files\NotAuthenticatedException {}
class InvalidOrExpiredCodeException extends \Files\NotAuthenticatedException {}
class InvalidUsernameOrPasswordException extends \Files\NotAuthenticatedException {}
class LockedOutException extends \Files\NotAuthenticatedException {}
class LockoutRegionMismatchException extends \Files\NotAuthenticatedException {}
class OneTimePasswordIncorrectException extends \Files\NotAuthenticatedException {}
class TwoFactorAuthenticationErrorException extends \Files\NotAuthenticatedException {}
class TwoFactorAuthenticationSetupExpiredException extends \Files\NotAuthenticatedException {}

} // namespace Files\NotAuthenticated

namespace Files\NotAuthorized {

class ApiKeyIsDisabledException extends \Files\NotAuthorizedException {}
class ApiKeyIsPathRestrictedException extends \Files\NotAuthorizedException {}
class ApiKeyOnlyForDesktopAppException extends \Files\NotAuthorizedException {}
class ApiKeyOnlyForMobileAppException extends \Files\NotAuthorizedException {}
class ApiKeyOnlyForOfficeIntegrationException extends \Files\NotAuthorizedException {}
class BillingPermissionRequiredException extends \Files\NotAuthorizedException {}
class BundleMaximumUsesReachedException extends \Files\NotAuthorizedException {}
class CannotLoginWhileUsingKeyException extends \Files\NotAuthorizedException {}
class CantActForOtherUserException extends \Files\NotAuthorizedException {}
class ContactAdminForPasswordChangeHelpException extends \Files\NotAuthorizedException {}
class FolderAdminOrBillingPermissionRequiredException extends \Files\NotAuthorizedException {}
class FolderAdminPermissionRequiredException extends \Files\NotAuthorizedException {}
class FullPermissionRequiredException extends \Files\NotAuthorizedException {}
class HistoryPermissionRequiredException extends \Files\NotAuthorizedException {}
class InsufficientPermissionForParamsException extends \Files\NotAuthorizedException {}
class MustAuthenticateWithApiKeyException extends \Files\NotAuthorizedException {}
class NeedAdminPermissionForInboxException extends \Files\NotAuthorizedException {}
class NonAdminsMustQueryByFolderOrPathException extends \Files\NotAuthorizedException {}
class NotAllowedToCreateBundleException extends \Files\NotAuthorizedException {}
class PasswordChangeNotRequiredException extends \Files\NotAuthorizedException {}
class PasswordChangeRequiredException extends \Files\NotAuthorizedException {}
class ReadOnlySessionException extends \Files\NotAuthorizedException {}
class ReadPermissionRequiredException extends \Files\NotAuthorizedException {}
class ReauthenticationFailedException extends \Files\NotAuthorizedException {}
class ReauthenticationFailedFinalException extends \Files\NotAuthorizedException {}
class ReauthenticationNeededActionException extends \Files\NotAuthorizedException {}
class SelfManagedRequiredException extends \Files\NotAuthorizedException {}
class SiteAdminRequiredException extends \Files\NotAuthorizedException {}
class SiteFilesAreImmutableException extends \Files\NotAuthorizedException {}
class TwoFactorAuthenticationRequiredException extends \Files\NotAuthorizedException {}
class UserIdWithoutSiteAdminException extends \Files\NotAuthorizedException {}
class WriteAndBundlePermissionRequiredException extends \Files\NotAuthorizedException {}
class WritePermissionRequiredException extends \Files\NotAuthorizedException {}
class ZipDownloadIpMismatchException extends \Files\NotAuthorizedException {}

} // namespace Files\NotAuthorized

namespace Files\NotFound {

class ApiKeyNotFoundException extends \Files\NotFoundException {}
class BundlePathNotFoundException extends \Files\NotFoundException {}
class BundleRegistrationNotFoundException extends \Files\NotFoundException {}
class CodeNotFoundException extends \Files\NotFoundException {}
class FileNotFoundException extends \Files\NotFoundException {}
class FileUploadNotFoundException extends \Files\NotFoundException {}
class FolderNotFoundException extends \Files\NotFoundException {}
class GroupNotFoundException extends \Files\NotFoundException {}
class InboxNotFoundException extends \Files\NotFoundException {}
class NestedNotFoundException extends \Files\NotFoundException {}
class PlanNotFoundException extends \Files\NotFoundException {}
class SiteNotFoundException extends \Files\NotFoundException {}
class UserNotFoundException extends \Files\NotFoundException {}

} // namespace Files\NotFound

namespace Files\ProcessingFailure {

class AutomationCannotBeRunManuallyException extends \Files\ProcessingFailureException {}
class BundleOnlyAllowsPreviewsException extends \Files\ProcessingFailureException {}
class BundleOperationRequiresSubfolderException extends \Files\ProcessingFailureException {}
class CouldNotCreateParentException extends \Files\ProcessingFailureException {}
class DestinationExistsException extends \Files\ProcessingFailureException {}
class DestinationFolderLimitedException extends \Files\ProcessingFailureException {}
class DestinationParentConflictException extends \Files\ProcessingFailureException {}
class DestinationParentDoesNotExistException extends \Files\ProcessingFailureException {}
class ExpiredPrivateKeyException extends \Files\ProcessingFailureException {}
class ExpiredPublicKeyException extends \Files\ProcessingFailureException {}
class ExportFailureException extends \Files\ProcessingFailureException {}
class ExportNotReadyException extends \Files\ProcessingFailureException {}
class FailedToChangePasswordException extends \Files\ProcessingFailureException {}
class FileLockedException extends \Files\ProcessingFailureException {}
class FileNotUploadedException extends \Files\ProcessingFailureException {}
class FilePendingProcessingException extends \Files\ProcessingFailureException {}
class FileTooBigToDecryptException extends \Files\ProcessingFailureException {}
class FileTooBigToEncryptException extends \Files\ProcessingFailureException {}
class FileUploadedToWrongRegionException extends \Files\ProcessingFailureException {}
class FolderLockedException extends \Files\ProcessingFailureException {}
class FolderNotEmptyException extends \Files\ProcessingFailureException {}
class HistoryUnavailableException extends \Files\ProcessingFailureException {}
class InvalidBundleCodeException extends \Files\ProcessingFailureException {}
class InvalidFileTypeException extends \Files\ProcessingFailureException {}
class InvalidFilenameException extends \Files\ProcessingFailureException {}
class InvalidRangeException extends \Files\ProcessingFailureException {}
class ModelSaveErrorException extends \Files\ProcessingFailureException {}
class MultipleProcessingErrorsException extends \Files\ProcessingFailureException {}
class PathTooLongException extends \Files\ProcessingFailureException {}
class RecipientAlreadySharedException extends \Files\ProcessingFailureException {}
class RemoteServerErrorException extends \Files\ProcessingFailureException {}
class ResourceLockedException extends \Files\ProcessingFailureException {}
class SubfolderLockedException extends \Files\ProcessingFailureException {}
class TwoFactorAuthenticationCodeAlreadySentException extends \Files\ProcessingFailureException {}
class TwoFactorAuthenticationCountryBlacklistedException extends \Files\ProcessingFailureException {}
class TwoFactorAuthenticationGeneralErrorException extends \Files\ProcessingFailureException {}
class UpdatesNotAllowedForRemotesException extends \Files\ProcessingFailureException {}

} // namespace Files\ProcessingFailure

namespace Files\RateLimited {

class DuplicateShareRecipientException extends \Files\RateLimitedException {}
class ReauthenticationRateLimitedException extends \Files\RateLimitedException {}
class TooManyConcurrentRequestsException extends \Files\RateLimitedException {}
class TooManyLoginAttemptsException extends \Files\RateLimitedException {}
class TooManyRequestsException extends \Files\RateLimitedException {}
class TooManySharesException extends \Files\RateLimitedException {}

} // namespace Files\RateLimited

namespace Files\ServiceUnavailable {

class AutomationsUnavailableException extends \Files\ServiceUnavailableException {}
class UploadsUnavailableException extends \Files\ServiceUnavailableException {}

} // namespace Files\ServiceUnavailable

namespace Files\SiteConfiguration {

class AccountAlreadyExistsException extends \Files\SiteConfigurationException {}
class AccountOverdueException extends \Files\SiteConfigurationException {}
class NoAccountForSiteException extends \Files\SiteConfigurationException {}
class SiteWasRemovedException extends \Files\SiteConfigurationException {}
class TrialExpiredException extends \Files\SiteConfigurationException {}
class TrialLockedException extends \Files\SiteConfigurationException {}
class UserRequestsEnabledRequiredException extends \Files\SiteConfigurationException {}

} // namespace Files\SiteConfiguration
