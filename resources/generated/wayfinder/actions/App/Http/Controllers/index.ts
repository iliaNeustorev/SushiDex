import PostController from './PostController'
import GeneralController from './GeneralController'
import Admin from './Admin'
import PostTrashController from './PostTrashController'
import Auth from './Auth'

const Controllers = {
    PostController: Object.assign(PostController, PostController),
    GeneralController: Object.assign(GeneralController, GeneralController),
    Admin: Object.assign(Admin, Admin),
    PostTrashController: Object.assign(PostTrashController, PostTrashController),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers