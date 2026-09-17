import PostController from './PostController'
import ImagesController from './ImagesController'
import DashboardController from './DashboardController'
import TagController from './TagController'
import CategoryController from './CategoryController'
import Trash from './Trash'
import ProductController from './ProductController'
import UserController from './UserController'

const Admin = {
    PostController: Object.assign(PostController, PostController),
    ImagesController: Object.assign(ImagesController, ImagesController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    TagController: Object.assign(TagController, TagController),
    CategoryController: Object.assign(CategoryController, CategoryController),
    Trash: Object.assign(Trash, Trash),
    ProductController: Object.assign(ProductController, ProductController),
    UserController: Object.assign(UserController, UserController),
}

export default Admin