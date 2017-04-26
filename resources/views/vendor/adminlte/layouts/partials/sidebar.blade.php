<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        @if (! Auth::guest())
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ Gravatar::get(Auth::user()->email) }}" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->name }}</p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> {{ Auth::user()->role }}</a>
                </div>
            </div>
        @endif

        <!-- search form (Optional) --
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="{{ trans('adminlte_lang::message.search') }}..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">{{ trans('adminlte_lang::message.cia') }}</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="active"><a href="{{ url('home/order/hotel') }}"><i class='fa fa-link'></i> <span>{{ trans('adminlte_lang::message.createclaim') }}</span></a></li>

            <li class="treeview">
                <a href="{{ url('home') }}"><i class='fa fa-link'></i> <span>{{ trans('adminlte_lang::message.viewclaim') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ URL::to('home/claim/list/1') }}">{{ trans('adminlte_lang::message.sent') }}</a></li>
                    <li><a href="{{ URL::to('home/claim/list/2') }}">{{ trans('adminlte_lang::message.approved') }}</a></li>
					<li><a href="{{ URL::to('home/claim/list/3') }}">{{ trans('adminlte_lang::message.reported') }}</a></li>
					<li><a href="{{ URL::to('home/claim/list/4') }}">{{ trans('adminlte_lang::message.disbursed') }}</a></li>
					<li><a href="{{ URL::to('home/claim/list/5') }}">{{ trans('adminlte_lang::message.closed') }}</a></li>
					<li><a href="{{ URL::to('home/claim/list/6') }}">{{ trans('adminlte_lang::message.rejected') }}</a></li>
                </ul>
            </li>

			@if (Auth::user()->role=="approver" || Auth::user()->role=="admin" )
			<li class="treeview">
                <a href="#"><i class='fa fa-link'></i> <span>{{ trans('adminlte_lang::message.approver') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{URL::to('home/approver/received')}}">{{ trans('adminlte_lang::message.received') }}</a></li>
                    <li><a href="{{URL::to('home/approver/approved')}}">{{ trans('adminlte_lang::message.approved') }}</a></li>
					<li><a href="{{URL::to('home/approver/rejected')}}">{{ trans('adminlte_lang::message.rejected') }}</a></li>
                </ul>
            </li>
			@endif

			@if (Auth::user()->role=="finance" || Auth::user()->role=="admin")
            <li class="treeview">
				<a href="#"><i class='fa fa-link'></i> <span>{{ trans('adminlte_lang::message.finance') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{URL::to('home/approver/received')}}">{{ trans('adminlte_lang::message.received') }}</a></li>
                    <li><a href="{{URL::to('home/approver/approved')}}">{{ trans('adminlte_lang::message.approved') }}</a></li>
					<li><a href="{{URL::to('home/approver/rejected')}}">{{ trans('adminlte_lang::message.rejected') }}</a></li>
                </ul>
            </li>
			@endif
			<li class="active"><a href="home/settings"><i class='fa fa-link'></i> <span>{{ trans('adminlte_lang::message.settings') }}</span></a></li>

            <li class="active"><a href="{{ url('/logout') }}" onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();"><i class='fa fa-link'></i> <span>{{ trans('adminlte_lang::message.signout') }}</span></a></li>

        </ul><!-- /.sidebar-menu -->

    </section>
    <!-- /.sidebar -->
</aside>
