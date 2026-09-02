import PostController from './PostController'
import ImagesController from './ImagesController'
import DashboardController from './DashboardController'
import TagController from './TagController'
import CategoryController from './CategoryController'
import ProductController from './ProductController'

const Admin = {
    PostController: Object.assign(PostController, PostController),
    ImagesController: Object.assign(ImagesController, ImagesController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    TagController: Object.assign(TagController, TagController),
    CategoryController: Object.assign(CategoryController, CategoryController),
    ProductController: Object.assign(ProductController, ProductController),
}

export default Admin