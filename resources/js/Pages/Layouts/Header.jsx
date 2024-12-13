import React, { useState } from 'react';
import '@fortawesome/fontawesome-free/css/all.min.css';
import { usePage, Link } from '@inertiajs/react';
import { Dropdown, Menu, Button, Input, AutoComplete } from 'antd';
import { SearchOutlined, HeartOutlined, UserOutlined, LoginOutlined, UserAddOutlined, BarsOutlined } from '@ant-design/icons';
import GetListCategory from '@/Components/GetListCategory';
import CartCount from '@/Components/CountCart';
import SearchInput from '@/Components/SearchInput';
const HeaderLayout = () => {
    const loginUrl = '/login';
    const registerUrl = '/register';
    const { props } = usePage();
    const user = props.auth.user;
    const [productResult, setProductResult] = useState([]);

    const navigateTo = (url) => {
        window.location.href = `${window.location.origin}${url}`;
    };



    const userMenu = (
        <Menu>
            {user && user.status == 'admin' && (<Menu.Item key="admin"> <Link href={route('admin.index')}>Quản trị viên</Link> </Menu.Item>
            )}
            <Menu.Item key="profile">
                <Link href={route('profile.edit')}>Hồ sơ cá nhân</Link>
            </Menu.Item>
            <Menu.Item key="order-history">
                <Link href={route('order.history')}>Lịch sử mua hàng</Link>
            </Menu.Item>
            <Menu.Item key="order-history">
                <Link href={route('auth.newpassword')}>Đổi mật khẩu </Link>
            </Menu.Item>
            <Menu.Item key="logout">
                <Link href={route('logout')} method="post" as="button">Đăng xuất</Link>
            </Menu.Item>
        </Menu>
    );

    const categoryMenu = (
        <Menu>
            <Menu.Item key="category">
                <GetListCategory />
            </Menu.Item>
        </Menu>
    );

    const productMenu = (
        <Menu>
            {productResult.length > 0 ? (
                productResult.map((product, index) => (
                    <Menu.Item key={index}>
                        <a href={`/product/${product.id}`} className="text-gray-800">
                            {product.name}
                        </a>
                    </Menu.Item>
                ))
            ) : (
                <Menu.Item key="no-results" disabled>
                    Không tìm thấy sản phẩm
                </Menu.Item>
            )}
        </Menu>
    );

    return (
        <header className="bg-gray-900 text-gray-200">
            <div className="py-2 px-4 bg-gray-800">
                <div className="container mx-auto flex justify-between items-center">
                    <div className="flex items-center space-x-4 text-sm">
                        <div className="flex items-center">
                            <i className="fas fa-map-marker-alt text-gray-400"></i>
                            <span className="ml-2">15 Nguyễn Lương Bằng, Đà Nẵng</span>
                        </div>
                        <div className="flex items-center">
                            <i className="fas fa-clock text-gray-400"></i>
                            <span className="ml-2">Thời gian mở cửa 10:00 - 18:00</span>
                        </div>
                    </div>
                    <div className="flex items-center space-x-6 text-gray-400">
                        <span className="font-semibold">+1 900 777525</span>
                        <div className="flex items-center space-x-3">
                            {['youtube', 'facebook', 'telegram', 'instagram', 'twitter'].map((platform) => (
                                <a key={platform} href="#" className="hover:text-blue-500">
                                    <i className={`fab fa-${platform}`}></i>
                                </a>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            <div className="py-4 px-8 bg-white">
                <div className="flex justify-between items-center">
                    <Link href={route('page.home')} className="cursor-pointer flex items-center">
                        <span className="text-3xl font-bold text-blue-400 tracking-wide">MAGIC STORE</span>
                    </Link>

                    <div className="flex w-1/3 gap-2 items-center relative">
                        <SearchInput />
                    </div>

                    <div className="flex items-center space-x-4 text-gray-400">
                        <Link href={route('wishlist')} className="cursor-pointer flex items-center">
                            <Button
                                icon={<HeartOutlined />}
                                shape="circle"
                                className="text-gray-400 hover:text-red-400"
                            />
                        </Link>
                        <CartCount />
                    </div>
                </div>
            </div>

            <div className="bg-gray-800 py-3">
                <div className="flex justify-between items-center px-8">
                    <Dropdown overlay={categoryMenu} trigger={['click']}>
                        <Button icon={<BarsOutlined />} className="text-gray-700 hover:text-blue-400" />
                    </Dropdown>

                    <ul className="flex space-x-8 text-sm text-white">
                        <Link className='hover:text-blue-400' href={route('page.home')}>Trang chủ</Link>
                        <Link className='hover:text-blue-400' href={route('page.products')}>Sản phẩm</Link>
                        <Link className='hover:text-blue-400' href={route('page.about')}>Giới thiệu</Link>
                        <Link className='hover:text-blue-400' href={route('page.support')}>Hỗ Trợ</Link>
                        <Link className='hover:text-blue-400' href={route('page.contact')}>Liên hệ</Link>
                    </ul>

                    <div className="flex space-x-4 items-center text-white">
                        {!user ? (
                            <>
                                <Button icon={<LoginOutlined />} onClick={() => navigateTo(loginUrl)} />
                                <Button icon={<UserAddOutlined />} onClick={() => navigateTo(registerUrl)} />
                            </>
                        ) : (
                            <Dropdown overlay={userMenu} trigger={['click']}>
                                <Button icon={<UserOutlined />} className="text-gray-700 hover:text-blue-400">
                                    {/* <span className="ml-2">{user.name}</span> */}
                                </Button>
                            </Dropdown>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
};

export default HeaderLayout;
