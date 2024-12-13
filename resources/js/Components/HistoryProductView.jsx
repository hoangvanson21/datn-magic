import React from 'react';
import '@fortawesome/fontawesome-free/css/all.min.css';
import { usePage, Link } from '@inertiajs/react';
import { Dropdown, Menu, Button } from 'antd';  // Import từ antd
import { SearchOutlined, HeartOutlined, UserOutlined, LoginOutlined, UserAddOutlined, BarsOutlined } from '@ant-design/icons'; // Import icon từ antd
import GetListCategory from '@/Components/GetListCategory';
import CartCount from '@/Components/CountCart';

const HeaderLayout = () => {
    const loginUrl = '/login';
    const registerUrl = '/register';
    const cartUrl = '/cart';
    const { props } = usePage();
    const user = props.auth.user;
    const isAdmin = props.isAdmin;

    const navigateTo = (url) => {
        window.location.href = `${window.location.origin}${url}`;
    };

    const userMenu = (
        <Menu>
            {isAdmin && (  // Kiểm tra nếu là admin
                <Menu.Item key="admin">
                    <Link href={route('admin.dashboard')}>Quản trị viên</Link>
                </Menu.Item>
            )}
            <Menu.Item key="profile">
                <Link href={route('profile.edit')}>Hồ sơ cá nhân</Link>
            </Menu.Item>
            <Menu.Item key="order-history">
                <Link href={route('order.history')}>Lịch sử mua hàng</Link>
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

            {/* Bottom section */}
            <div className="py-4 px-8 bg-white">
                <div className=" flex justify-between items-center">
                    {/* Logo */}
                    <Link href={route('page.home')} className="cursor-pointer flex items-center">
                        <span className="text-3xl font-bold text-blue-400 tracking-wide">MAGIC STORE</span>
                    </Link>

                    <div className="flex items-center">
                        <Button icon={<SearchOutlined />} shape="circle" />
                    </div>

                    {/* Icons */}
                    <div className="flex items-center space-x-4 text-gray-400">
                        <Button icon={<HeartOutlined />} shape="circle" className="text-gray-400 hover:text-red-400" />
                        <CartCount />
                    </div>
                </div>
            </div>

            <div className="bg-gray-800 py-3">
                <div className="flex justify-between items-center px-8">
                    {/* Dropdown for Categories */}
                    <Dropdown overlay={categoryMenu} trigger={['click']}>
                        <Button icon={<BarsOutlined />} className="text-white hover:text-blue-400" />
                    </Dropdown>

                    <ul className="flex space-x-8 text-sm text-white">
                        <Link className='hover:text-blue-400' href={route('page.home')}>Trang chủ</Link>
                        <Link className='hover:text-blue-400' href={route('page.products')}>Sản phẩm</Link>
                        <Link className='hover:text-blue-400' href={route('page.about')}>Giới thiệu</Link>
                        <Link className='hover:text-blue-400' href={route('page.support')}>Hỗ Trợ</Link>
                        <Link className='hover:text-blue-400' href={route('page.contact')}>Liên hệ</Link>
                    </ul>

                    {/* User Actions */}
                    <div className="flex space-x-4 items-center text-white">
                        {!user ? (
                            <>
                                <Button icon={<LoginOutlined />} onClick={() => navigateTo(loginUrl)} />
                                <Button icon={<UserAddOutlined />} onClick={() => navigateTo(registerUrl)} />
                            </>
                        ) : (
                            <Dropdown overlay={userMenu} trigger={['click']}>
                                <Button icon={<UserOutlined />} className="text-white hover:text-blue-400">
                                    <span className="ml-2">{user.name}</span>
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
