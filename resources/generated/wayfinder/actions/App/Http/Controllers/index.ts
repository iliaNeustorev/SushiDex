import Client from './Client'
import PostController from './PostController'
import GeneralController from './GeneralController'
import Admin from './Admin'
import Auth from './Auth'

const Controllers = {
    Client: Object.assign(Client, Client),
    PostController: Object.assign(PostController, PostController),
    GeneralController: Object.assign(GeneralController, GeneralController),
    Admin: Object.assign(Admin, Admin),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers