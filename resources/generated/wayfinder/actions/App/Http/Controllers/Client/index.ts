import ProfileController from './ProfileController'
import PhoneController from './PhoneController'

const Client = {
    ProfileController: Object.assign(ProfileController, ProfileController),
    PhoneController: Object.assign(PhoneController, PhoneController),
}

export default Client