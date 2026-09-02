import SessionController from './SessionController'
import RegisterController from './RegisterController'
import PasswordResetController from './PasswordResetController'
import NewPasswordController from './NewPasswordController'
import VerificationController from './VerificationController'

const Auth = {
    SessionController: Object.assign(SessionController, SessionController),
    RegisterController: Object.assign(RegisterController, RegisterController),
    PasswordResetController: Object.assign(PasswordResetController, PasswordResetController),
    NewPasswordController: Object.assign(NewPasswordController, NewPasswordController),
    VerificationController: Object.assign(VerificationController, VerificationController),
}

export default Auth